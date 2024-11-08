<?php

namespace App\Repositories;

use App\Library\FunctionUtils;
use App\Library\Setting;
use App\Models\ScorecardStack;
use App\Models\ScorecardStackNodeData;
use App\Models\ScorecardStackNodes;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;
use function App\Library\Setting;

class ScorecardStackRepository extends BaseRepository
{
    private $scorecardStack;

    public function __construct()
    {
        $this->scorecardStack = new ScorecardStack;
    }

    /**
     * List Scorcard Stack
     */

    public function list()
    {
        $query = \DB::table('scorecard_stack')
            ->select('scorecard_stack.id', 'scorecard_stack.company_id', 'scorecard_stack.project_id', 'scorecard_stack.company_stack_module_id', 'scorecard_stack.company_stack_category_id', 'scorecard_stack.scorecard_type', 'scorecard_stack.scorecard_data', 'scorecard_stack.is_active')
            ->whereNull('scorecard_stack.deleted_at');
        $data  = $query->get()->toArray();
        $count = $query->count();
        return ['data' => $data, 'count' => $count];
    }

    /**
     * Store Scorcard Stack
     */
    public function store($request)
    {
        $scorecardStartFrom = null;
        if (in_array($request->scorecard_type, [config('global.SCORECARD_TYPE.WEEKLY.id'), config('global.SCORECARD_TYPE.BI_WEEKLY.id')]) && !isset($request->scorecard_start_from)) {
            $scorecardStartFrom = config('global.DAYS.MONDAY.id');
        }
        if (isset($request->scorecard_start_from)) {
            $scorecardStartFrom = $request->scorecard_start_from;
        }

        return ScorecardStack::create([
            'company_id'           => $request->company_id,
            'project_id'           => $request->project_id,
            'project_category_id'  => $request->project_category_id,
            'scorecard_type'       => $request->scorecard_type,
            'scorecard_start_from' => $scorecardStartFrom,
            'scorecard_data'       => $request->scorecard_data,
        ]);
    }

    /**
     * Details Scorcard Stack
     */
    public function details($request)
    {
        $dataDetails = (is_array($request) || is_object($request)) ? $this->dataDetails($request) : $this->scorecardStack->find($request);
        if (empty($dataDetails)) {
            return null;
        }
        // \DB::enableQueryLog();
        $dataDetails->userAccess = $dataDetails->userAccess()->select(['users.id', \DB::raw("CONCAT(users.first_name,' ',users.last_name) as name"), \DB::raw("IF (users.profile_image IS NOT NULL, " . \DB::raw("CONCAT('" . FunctionUtils::getS3Url(config('global.UPLOAD_PATHS.USER_PROFILE')) . "', users.profile_image)") . ', NULL) AS profile_image'), 'users.email'])
            ->leftjoin('mst_users as users', 'users.id', '=', 'user_id')
            ->get();

        $dataDetails->canChangeScorecardType = empty($dataDetails->scorecardStackNodeData()->count());

        $scorecardData = json_decode($dataDetails->scorecard_data, true);
        $allNodesData  = $scorecardData['nodes'] ?? [];

        if (empty($request->from_date) && empty($request->to_date)) {
            $fromDateAndToDate = $this->getFromDateAndToDate($dataDetails->scorecard_type, Carbon::now()->format('Y-m-d'), Carbon::now()->format('Y-m-d'), $dataDetails->scorecard_start_from); // Carbon::parse('2023-01-19')
            $from_date         = $fromDateAndToDate['fromDate']->format('Y-m-d');
            $to_date           = $fromDateAndToDate['toDate']->format('Y-m-d');
        } else {
            $fromDateAndToDate = $this->getFromDateAndToDate($dataDetails->scorecard_type, Carbon::parse($request->from_date)->format('Y-m-d'), Carbon::parse($request->to_date)->format('Y-m-d'), $dataDetails->scorecard_start_from);
            $from_date         = $fromDateAndToDate['fromDate']->format('Y-m-d');
            $to_date           = $fromDateAndToDate['toDate']->format('Y-m-d');
        }

        $dateList    = [];
        $newFromDate = $fromDateAndToDate['fromDate'];
        while (1) {
            $newFromDateAndToDate = $this->getFromDateAndToDate($dataDetails->scorecard_type, $newFromDate, $newFromDate, $dataDetails->scorecard_start_from);
            if (Carbon::parse($newFromDateAndToDate['toDate'])->lte(Carbon::parse($fromDateAndToDate['toDate']))) {
                $dateList[] = ['from_date' => $newFromDateAndToDate['fromDate']->format('Y-m-d'), 'to_date' => $newFromDateAndToDate['toDate']->format('Y-m-d')];
            } else {
                break;
            }
            $newFromDate = Carbon::parse($newFromDateAndToDate['toDate'])->startOfDay()->addDay();
        }

        $scorecardStackNodes = $dataDetails->scorecardStackNodes()->with('assignedToUser:id,first_name,last_name,email,profile_image')->select('node_id', 'node_data', 'assigned_to')->get()->toArray();

        //        $scorecardStackNodeData = $dataDetails->scorecardStackNodeData()->select('scorecard_stack_node_data.id AS node_edit_id', 'scorecard_stack_node_data.node_id', 'scorecard_stack_node_data.value', 'scorecard_stack_node_data.from_date', 'scorecard_stack_node_data.to_date')
        //            ->join(\DB::raw('(SELECT MIN(id) AS last_id, scorecard_stack_id, node_id FROM scorecard_stack_node_data WHERE (from_date>="' . $from_date . '" AND to_date<="' . $to_date . '") GROUP BY scorecard_stack_id,node_id) AS first_data'), 'first_data.last_id', 'scorecard_stack_node_data.id')->get()->toArray();
        $scorecardStackNodeDataList = $dataDetails->scorecardStackNodeData()->select('scorecard_stack_node_data.id AS node_edit_id', 'scorecard_stack_node_data.node_id', 'scorecard_stack_node_data.value', 'scorecard_stack_node_data.from_date', 'scorecard_stack_node_data.to_date', 'scorecard_stack_node_data.assigned_color', 'scorecard_stack_node_data.comment');
        if (!empty($request->type) && $request->type == "GraphView") {
            $scorecardStackNodeDataList = $scorecardStackNodeDataList->orderBy('from_date', 'desc')->limit(52);
        } else {
            if (empty($request->display_data) || (!empty($request->display_data) && $request->display_data != "all")) {
                $scorecardStackNodeDataList = $scorecardStackNodeDataList->where(function ($query) use ($from_date, $to_date) {
                    $query->where('from_date', '>=', $from_date);
                    $query->where('to_date', '<=', $to_date);
                })->orderBy('from_date');
            } else {
                $scorecardStackNodeDataList = $scorecardStackNodeDataList->orderBy('from_date', 'desc');
            }
        }
        $scorecardStackNodeDataList = $scorecardStackNodeDataList->get()->toArray();
        $metricValuesToCalculate    = [];
        for ($i = 0; $i < sizeof($allNodesData); $i++) {
            if (isset($allNodesData[$i]['type']) && $allNodesData[$i]['type'] != 'spacer') {
                $nodeData = $allNodesData[$i]['data'];
                $nodeType = $nodeData['type'];
                $nodeId   = $nodeData['node_id'];

                if ($nodeType === "metricBox") {
                    $nodeAllDataList = array_values(array_filter($scorecardStackNodeDataList, function ($item) use ($nodeId) {
                        if ($item['node_id'] == $nodeId) {
                            return true;
                        } else {
                            return false;
                        }
                    }));
                    $nodeKey         = array_search($nodeId, array_column($scorecardStackNodes, 'node_id'));
                    $avatar          = ($nodeKey !== false && $scorecardStackNodes[$nodeKey]['assigned_to_user'] && $scorecardStackNodes[$nodeKey]['assigned_to_user']['profile_image']) ? $scorecardStackNodes[$nodeKey]['assigned_to_user']['profile_image'] : null;
                    $users           = ($nodeKey !== false && $scorecardStackNodes[$nodeKey]['assigned_to_user']) ? $scorecardStackNodes[$nodeKey]['assigned_to_user'] : null;
                    $userImage       = $avatar ? FunctionUtils::getS3FileUrl(config('global.UPLOAD_PATHS.USER_PROFILE') . $avatar) : null;

                    $nodeKey !== false && $nodeData['avatar'] = $userImage;
                    $nodeKey !== false && $nodeData['user']['name'] = $users ? $users['first_name'] . " " . $users['last_name'] : "";
                    $nodeKey !== false && $nodeData['user']['email'] = $users ? $users['email'] : "";
                    $nodeKey !== false && $nodeData['user']['profile_image'] = $users ? $userImage : "";
                    if (!empty($nodeAllDataList) && ($nodeAllDataList[0]['from_date'] == $dateList[0]['from_date'] || ((!empty($request->type) && $request->type == "GraphView") || (!empty($request->display_data) && $request->display_data == "all")))) {
                        $nodeEditId                 = $nodeAllDataList[0]['node_edit_id'];
                        $nodeData['node_edit_id']   = $nodeEditId;
                        $nodeData['value']          = number_format($nodeAllDataList[0]['value']);
                        $nodeData['edit_value']     = $nodeAllDataList[0]['value'];
                        $nodeData['from_date']      = $nodeAllDataList[0]['from_date'];
                        $nodeData['to_date']        = $nodeAllDataList[0]['to_date'];
                        $nodeData['assigned_color'] = $nodeAllDataList[0]['assigned_color'];
                        $nodeData['comment']        = $nodeAllDataList[0]['comment'];
                    } else {
                        $nodeEditId               = null;
                        $nodeData['node_edit_id'] = $nodeEditId;
                        $nodeData['value']        = 0;
                        $nodeData['edit_value']   = 0;
                        $nodeData['from_date']    = $dateList[0]['from_date'];
                        $nodeData['to_date']      = $dateList[0]['to_date'];
//                        $nodeData['assigned_color'] = null; /// dont assign any value to assigned_color other wise it will make issue
                        $nodeData['comment'] = null;
                    }
                    /// Above Goal = ((currentValue/goalValue)*100)
                    /// Below Goal = (1−((currentValue−goalValue)/100))*100

                    $nodeData['goal_difference'] = 0;
                    $nodeData['goal_percentage'] = 0;
                    $goal_achieve_value          = $nodeData['goal'];
                    if (isset($nodeData['is_goal']) && $nodeData['is_goal'] == 1) {
                        $nodeData['goal_difference'] = $nodeData['edit_value'] - $goal_achieve_value;
                        if (isset($nodeData['is_goal']) && $nodeData['is_goal'] == 1 && $nodeData['goal_type'] == 1) {
                            $nodeData['goal_percentage'] = $nodeData['goal'] > 0 ? number_format((($nodeData['edit_value'] / $nodeData['goal']) * 100), 2) : 0;
                        }
                        if (isset($nodeData['is_goal']) && $nodeData['is_goal'] == 1 && $nodeData['goal_type'] == 2) {
                            $nodeData['goal_percentage'] = number_format(((1 - (($nodeData['edit_value'] - $nodeData['goal']) / 100)) * 100), 2);
                        }
                    }

                    if ($nodeData['auto_assign_color']) {
                        if ($nodeData['goal_percentage'] < 33) {
                            $nodeData['assigned_color'] = 'colorPink';
                        } else if ($nodeData['goal_percentage'] >= 33 && $nodeData['goal_percentage'] <= 66) {
                            $nodeData['assigned_color'] = 'colorOrange';
                        } else if ($nodeData['goal_percentage'] > 66) {
                            $nodeData['assigned_color'] = 'colorGreen';
                        }
                    }

                    $metricValuesToCalculate[$nodeData['from_date'] . '-to-' . $nodeData['to_date']][] = ['id' => $nodeData['id'], 'node_id' => $nodeData['node_id'], 'name' => $nodeData['name'], 'value' => $nodeData['edit_value'], 'from_date' => $nodeData['from_date'], 'to_date' => $nodeData['to_date']];

                    $dataList = array_filter($nodeAllDataList, function ($item) use ($nodeEditId) {
                        if ($item['node_edit_id'] != $nodeEditId) {
                            return true;
                        } else {
                            return false;
                        }
                    });

                    $nodeDataList = [];
                    $dataList     = array_values(array_map(function ($item) use ($nodeData) {
                        $item['goal_difference'] = 0;
                        $item['goal_percentage'] = 0;
                        $item['edit_value']      = $item['value'];
                        $item['value']           = number_format($item['value']);
                        if ($nodeData['is_goal'] == 1) {
                            $item['goal_difference'] = $item['edit_value'] - $nodeData['goal'];
                            if ($nodeData['is_goal'] == 1 && $nodeData['goal_type'] == 1) {
                                $item['goal_percentage'] = $nodeData['goal'] > 0 ? number_format((($item['edit_value'] / $nodeData['goal']) * 100), 2) : 0;
                            }
                            if ($nodeData['is_goal'] == 1 && $nodeData['goal_type'] == 2) {
                                $item['goal_percentage'] = number_format(((1 - (($item['edit_value'] - $nodeData['goal']) / 100)) * 100), 2);
                            }
                        }
                        return $item;
                    }, $dataList));

                    if ((!empty($request->type) && $request->type == "GraphView") || (!empty($request->display_data) && $request->display_data == "all")) {
                        $nodeDataList = $dataList;
                    } else {
                        $oldFromDate = array_column($dataList, 'from_date');
                        for ($k = 1; $k < sizeof($dateList); $k++) {
                            $key = array_search($dateList[$k]['from_date'], $oldFromDate);
                            if ($key !== false) {
                                $nodeDatesData = $dataList[$key];
                            } else {
                                $nodeDatesData = [
                                    // "node_edit_id"         => null,
                                    "node_id"         => $nodeId,
                                    "value"           => 0,
                                    "edit_value"      => 0,
                                    "from_date"       => $dateList[$k]['from_date'],
                                    "to_date"         => $dateList[$k]['to_date'],
                                    "assigned_color"  => null,
                                    "goal_difference" => 0,
                                    "goal_percentage" => 0,
                                ];
                            }

                            $metricValuesToCalculate[$nodeDatesData['from_date'] . '-to-' . $nodeDatesData['to_date']][] = ['id' => $nodeData['id'], 'node_id' => $nodeData['node_id'], 'name' => $nodeData['name'], 'value' => $nodeDatesData['edit_value'], 'from_date' => $nodeDatesData['from_date'], 'to_date' => $nodeDatesData['to_date']];

                            $nodeDataList[] = $nodeDatesData;
                        }
                    }
                    $nodeData['data_list']    = $nodeDataList;
                    $allNodesData[$i]['data'] = $nodeData;
                }
            }
        }
        $metricValuesToCalculate = array_values($metricValuesToCalculate);
        $tempAllNodesData        = [];
        for ($i = 0; $i < sizeof($allNodesData); $i++) {
            if (isset($allNodesData[$i]['type']) && $allNodesData[$i]['type'] != 'spacer') {
                $nodeType = $allNodesData[$i]['data']['type'];

                if ($nodeType === "metricBoxCalculation") {
                    $formula = $allNodesData[$i]['data']['expression_data'] ?? null;
                    if (!is_array($formula)) {
                        $formula = json_decode($formula, true);
                    }
                    $value = !empty($formula) ? Setting::calculateValue(Setting::getParsedReadableFormula($formula, $metricValuesToCalculate[0] ?? [], 'value')) : 0;
                    if (!empty($allNodesData[$i]['data']['format_of_metric'])) {
                        if ($allNodesData[$i]['data']['format_of_metric'] == config('global.FORMAT_OF_MATRIX.PERCENTAGE.id')) {
                            $value = $value * 100;
                        }
                    }
                    $allNodesData[$i]['data']['value']               = number_format($value, 2);
                    $allNodesData[$i]['data']['expression_readable'] = !empty($formula) ? Setting::getParsedReadableFormula($formula, $metricValuesToCalculate[0] ?? [], 'name') : "";
                    $allNodesData[$i]['data']['expression_data']     = $formula;
                    $allNodesData[$i]['data']['expression']          = !empty($formula) ? Setting::getParsedArithmeticFormula($formula, 'both') : "";
                    $allNodesData[$i]['data']['from_date']           = $metricValuesToCalculate[0][0]['from_date'] ?? "";
                    $allNodesData[$i]['data']['to_date']             = $metricValuesToCalculate[0][0]['to_date'] ?? "";

                    $calculationDataList = [];
                    for ($ic = 1; $ic < sizeof($metricValuesToCalculate); $ic++) {
                        $dateCalculateValue = !empty($formula) ? Setting::calculateValue(Setting::getParsedReadableFormula($formula, $metricValuesToCalculate[$ic], 'value')) : 0;
                        if (!empty($allNodesData[$i]['data']['format_of_metric'])) {
                            if ($allNodesData[$i]['data']['format_of_metric'] == config('global.FORMAT_OF_MATRIX.PERCENTAGE.id')) {
                                $dateCalculateValue = $dateCalculateValue * 100;
                            }
                        }
                        $calculationDataList[] = [
                            "value"     => number_format($dateCalculateValue, 2),
                            "from_date" => $metricValuesToCalculate[$ic][0]['from_date'] ?? "",
                            "to_date"   => $metricValuesToCalculate[$ic][0]['to_date'] ?? "",
                        ];
                    }
                    $allNodesData[$i]['data']['data_list'] = $calculationDataList;
                }

                if (isset($allNodesData[$i]['data']['data_list'])) {
                    $temp = $allNodesData[$i];
                    unset($temp['data']['data_list']);
                    $tempAllNodesData[] = $temp;
                } else {
                    $tempAllNodesData[] = $allNodesData[$i];
                }
            } else {
                $tempAllNodesData[] = $allNodesData[$i];
            }
        }
        $dataDetails->scorecard_from_and_to = ['from_date' => $from_date, 'to_date' => $to_date];

        $oldScorecardData = $dataDetails->scorecard_data;
        $newScorecardData = json_encode(['edges' => $scorecardData['edges'] ?? [], 'nodes' => $tempAllNodesData]);
        if (json_encode(json_decode($oldScorecardData)) != $newScorecardData) {
            $this->scorecardStack->find($dataDetails->id)->update([
                'scorecard_data' => $newScorecardData,
            ]);

            \DB::select('call scorecardStackNodeSaveProcedureV1(?)', array($dataDetails->id));
        }

        $dataDetails->scorecard_data = json_encode(['edges' => $scorecardData['edges'] ?? [], 'nodes' => $allNodesData]);
        $dataDetails->dateList       = $dateList; ///Dont remove
        // $dataDetails->allNodesData   = $allNodesData;
        // $dataDetails->scorecardStackNodeDataList   = $scorecardStackNodeDataList;

        return $dataDetails;
    }

    /**
     * Update Scorcard Stack
     */
    public function update($id, $request)
    {
        $data = $this->scorecardStack->find($id);

        $scorecardStartFrom = null;
        if (in_array($request->scorecard_type, [config('global.SCORECARD_TYPE.WEEKLY.id'), config('global.SCORECARD_TYPE.BI_WEEKLY.id')]) && !isset($request->scorecard_start_from)) {
            $scorecardStartFrom = config('global.DAYS.MONDAY.id');
        }
        if (isset($request->scorecard_start_from)) {
            $scorecardStartFrom = $request->scorecard_start_from;
        }

        $data->update([
            'company_id'           => $request->company_id,
            'project_id'           => $request->project_id,
            'project_category_id'  => $request->project_category_id,
            'scorecard_type'       => $request->scorecard_type,
            'scorecard_start_from' => $scorecardStartFrom,
            'scorecard_data'       => $request->scorecard_data,
        ]);
        return $data;
    }

    /**
     * Delete Scorcard Stack
     */
    public function destroy($id)
    {
        return $this->scorecardStack->find($id)->delete();
    }

    public function save($request)
    {
        $query = ScorecardStack::where('company_id', $request->company_id)
            ->where('project_id', $request->project_id)
            ->where('company_stack_module_id', $request->company_stack_modules_id)
            ->where('company_stack_category_id', $request->company_stack_category_id)->first();

        $scorecardStartFrom = null;
        if (in_array($request->scorecard_type, [config('global.SCORECARD_TYPE.WEEKLY.id'), config('global.SCORECARD_TYPE.BI_WEEKLY.id')]) && !isset($request->scorecard_start_from)) {
            $scorecardStartFrom = config('global.DAYS.MONDAY.id');
        }
        if (isset($request->scorecard_start_from)) {
            $scorecardStartFrom = $request->scorecard_start_from;
        }
        $nodeDetails = json_decode($request->scorecard_data);
        if (!empty($nodeDetails)) {
            foreach ($nodeDetails->nodes as $nodes) {
                if (isset($nodes->data->data_list)) {
                    unset($nodes->data->data_list);
                }
            }
        }

        if (empty($query)) {
            $data = ScorecardStack::create([
                'company_id'                => $request->company_id,
                'project_id'                => $request->project_id,
                'company_stack_module_id'   => $request->company_stack_modules_id,
                'company_stack_category_id' => $request->company_stack_category_id,
                'scorecard_type'            => $request->scorecard_type,
                'scorecard_start_from'      => $scorecardStartFrom,
                'scorecard_data'            => json_encode($nodeDetails),
            ]);
        } else {
            $data = $this->dataDetails($request);
            $data->update([
                'scorecard_type'       => $request->scorecard_type,
                'scorecard_start_from' => $scorecardStartFrom,
                'scorecard_data'       => json_encode($nodeDetails),
            ]);
        }

        if (!empty($nodeDetails)) {
            foreach ($nodeDetails->nodes as $nodes) {
                if (isset($nodes->data->type) && !empty($nodes->data->type) && $nodes->data->type == 'metricBox' && $nodes->data->value != 0) {
                    if (empty($nodes->data->node_edit_id)) {
                        ScorecardStackNodeData::create([
                            'scorecard_stack_id' => $data->id,
                            'node_id'            => $nodes->data->node_id,
                            'value'              => $nodes->data->edit_value,
                            'assigned_color'     => $nodes->data->assigned_color,
                            'from_date'          => $nodes->data->from_date,
                            'to_date'            => $nodes->data->to_date,
                        ]);
                    } else {
                        ScorecardStackNodeData::find($nodes->data->node_edit_id)
                            ->update([
                                'scorecard_stack_id' => $data->id,
                                'node_id'            => $nodes->data->node_id,
                                'value'              => $nodes->data->edit_value,
                                'assigned_color'     => $nodes->data->assigned_color,
                                'from_date'          => $nodes->data->from_date,
                                'to_date'            => $nodes->data->to_date,
                            ]);
                    }
                }
            }
        }

        //        $dataDetails = json_decode($request->scorecard_data);
        //        $this->addScorecardStackNodes($data->id, $dataDetails);

        \DB::select('call scorecardStackNodeSaveProcedureV1(?)', array($data->id));
        return $data;
    }

    //    public function addScorecardStackNodes($scorecardId, $nodeDetails)
    //    {
    //        // dispatch(new \App\Jobs\ScorecardStackNodesQueue($scorecardId, $nodeDetails));
    //        $nodeArray = array();
    //        foreach ($nodeDetails->nodes as $nodes) {
    //            $nodeType = $nodes->data->type;
    //            if (!empty($nodeType) && $nodeType == 'metricBox') {
    //                $nodeArray[] = $nodes->data->node_id;
    //
    //                ScorecardStackNodes::withTrashed()->updateOrCreate(
    //                    [
    //                        'node_id' => $nodes->data->node_id,
    //                    ],
    //                    [
    //                        'scorecard_stack_id'     => $scorecardId,
    //                        'node_data'              => json_encode($nodes),
    //                        'auto_assign_color'      => $nodes->data->auto_assign_color,
    //                        'assigned_color'         => $nodes->data->assigned_color,
    //                        'assigned_to'            => $nodes->data->assigned_to,
    //                        'goal_achieve_in_number' => $nodes->data->goal_achieve_in_number,
    //                        'reminder'               => $nodes->data->reminder,
    //                    ]
    //                );
    //            }
    //                }
    //        ScorecardStackNodes::where('scorecard_stack_id', $scorecardId)->whereNotIn('node_id', $nodeArray)->delete();
    //    }

    public function dataDetails($request)
    {
        $query = ScorecardStack::where('company_id', $request->company_id)
            ->where('project_id', $request->project_id)
            ->where('company_stack_module_id', $request->company_stack_modules_id)
            ->where('company_stack_category_id', $request->company_stack_category_id)->first();

        if (empty($query)) {
            return null;
        }
        return $query;
    }

    public function nodeEntry($request)
    {
        return ScorecardStackNodeData::create([
            'scorecard_stack_id' => $request->scorecard_stack_id,
            'node_id'            => $request->node_id,
            'value'              => $request->value,
            'comment'            => $request->comment,
            'assigned_color'     => $request->assigned_color,
            'from_date'          => $request->from_date,
            'to_date'            => $request->to_date,
        ]);
    }

    public function getFromDateAndToDate($scorecardType, $fromDate, $toDate, $scorecardStartFrom)
    {
        if ($scorecardStartFrom == Carbon::SUNDAY) {
            $scorecardEnds = Carbon::SATURDAY;
        } elseif ($scorecardStartFrom == Carbon::MONDAY) {
            $scorecardEnds = Carbon::SUNDAY;
        } elseif ($scorecardStartFrom == Carbon::TUESDAY) {
            $scorecardEnds = Carbon::MONDAY;
        } elseif ($scorecardStartFrom == Carbon::WEDNESDAY) {
            $scorecardEnds = Carbon::TUESDAY;
        } elseif ($scorecardStartFrom == Carbon::THURSDAY) {
            $scorecardEnds = Carbon::WEDNESDAY;
        } elseif ($scorecardStartFrom == Carbon::FRIDAY) {
            $scorecardEnds = Carbon::THURSDAY;
        } else {
            $scorecardEnds = Carbon::FRIDAY;
        }

        if ($scorecardType == 1) {
            $from_date = Carbon::parse($fromDate)->startOfWeek($scorecardStartFrom);
            $to_date   = Carbon::parse($toDate)->endOfWeek($scorecardEnds);
        } else if ($scorecardType == 2) {
            $from_date = Carbon::parse($fromDate)->startOfWeek($scorecardStartFrom);
            $to_date   = Carbon::parse($toDate)->endOfWeek($scorecardEnds);
            while (1) {
                if ((($from_date->diff($to_date)->days + 1) % 14) != 0) {
                    $to_date = Carbon::parse($to_date)->addWeek();
                } else {
                    break;
                }
            }
        } else if ($scorecardType == 3) {
            if (Carbon::parse($fromDate)->format('d') > 15) {
                $from_date = Carbon::parse($fromDate)->startOfMonth()->addDays(15);
            } else {
                $from_date = Carbon::parse($fromDate)->startOfMonth();
            }
            if (Carbon::parse($toDate)->format('d') > 15) {
                $to_date = Carbon::parse($toDate)->endOfMonth();
            } else {
                $to_date = Carbon::parse($toDate)->startOfMonth()->addDays(14);
            }
        } else if ($scorecardType == 4) {
            $from_date = Carbon::parse($fromDate)->startOfMonth();
            $to_date   = Carbon::parse($toDate)->endOfMonth();
        } else if ($scorecardType == 5) {
            $from_date = Carbon::parse($fromDate)->startOfQuarter();
            $to_date   = Carbon::parse($toDate)->endOfQuarter();
        } else if ($scorecardType == 6) {
            $from_date = Carbon::parse($fromDate)->startOfYear();
            $to_date   = Carbon::parse($toDate)->endOfYear();
        } else {
            $from_date = Carbon::parse($fromDate)->startOfWeek($scorecardStartFrom);
            $to_date   = Carbon::parse($toDate)->endOfWeek($scorecardEnds);
        }

        return ['fromDate' => $from_date, 'toDate' => $to_date];
    }

    public function updateScorecardNodeData($request)
    {
        $dataDetails = ScorecardStackNodeData::find($request->scorecard_node_data_id);

        if (empty($dataDetails)) {
            return null;
        }

        $dataDetails->update([
            'value'          => $request->value,
            'assigned_color' => $request->assigned_color,
            'comment'        => $request->comment,
            'from_date'      => $request->from_date,
            'to_date'        => $request->to_date,
        ]);

        return $dataDetails;
    }

    public function getDifference($type, $target_date, $created_date, $scorecardStartFrom)
    {
        $difference = 1;
        $getDate    = $this->getFromDateAndToDate($type, $created_date, $created_date, $scorecardStartFrom);
        $from_date  = $getDate['fromDate'];
        $to_date    = $getDate['toDate'];

        while (1) {
            if (Carbon::parse($target_date)->between($from_date, $to_date)) {
                break;
            }
            $newDate   = $this->getFromDateAndToDate($type, Carbon::parse($to_date)->addDay(), Carbon::parse($to_date)->addDay(), $scorecardStartFrom);
            $from_date = $newDate['fromDate'];
            $to_date   = $newDate['toDate'];
            $difference++;
        }
        return $difference;
    }
}
