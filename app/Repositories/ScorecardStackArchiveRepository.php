<?php

namespace App\Repositories;

use App\Models\CompanyStackCategory;
use App\Models\CompanyStackModules;
use App\Models\ScorecardStack;
use App\Models\ScorecardStackArchive;
use App\Models\ScorecardStackNodeData;
use App\Models\ScorecardStackNodes;
use App\Services\Web\ScorecardStackService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;

class ScorecardStackArchiveRepository extends BaseRepository
{
    private $scorecardStackArchive, $scorecardStackService;
    use CommonTrait;
    public function __construct()
    {
        $this->scorecardStackArchive = new ScorecardStackArchive();
        $this->scorecardStackService = new ScorecardStackService;
    }

    public function list($postData, $page = 1, $perPage = 10)
    {
        $query = \DB::table('scorecard_stack_archive')
            ->select(
                'scorecard_stack_archive.id',
                'scorecard_stack_archive.type',
                'scorecard_stack_archive.company_id',
                'scorecard_stack_archive.project_id',
                \DB::raw('(CASE
                    WHEN scorecard_stack_archive.type = ' . config('global.SCORECARD_ARCHIVE_TYPE.SCORECARD.id') . ' THEN "' . config('global.SCORECARD_ARCHIVE_TYPE.SCORECARD.name') . '"
                    WHEN scorecard_stack_archive.type = ' . config('global.SCORECARD_ARCHIVE_TYPE.METRIC.id') . ' THEN "' . config('global.SCORECARD_ARCHIVE_TYPE.METRIC.name') . '"
                    ELSE ""
                    END) AS type'),
                \DB::raw('(CASE
                    WHEN scorecard_stack_archive.type = ' . config('global.SCORECARD_ARCHIVE_TYPE.SCORECARD.id') . ' THEN company_stack_category.name
                    WHEN scorecard_stack_archive.type = ' . config('global.SCORECARD_ARCHIVE_TYPE.METRIC.id') . ' THEN JSON_UNQUOTE(IF(JSON_EXTRACT(scorecard_data, JSON_UNQUOTE(REPLACE(JSON_SEARCH(`scorecard_data`, "one", scorecard_stack_archive.node_id, NULL, "$.nodes"), ".id", ".data.name"))) IS NOT NULL, JSON_EXTRACT(scorecard_data, JSON_UNQUOTE(REPLACE(JSON_SEARCH(`scorecard_data`, "one", scorecard_stack_archive.node_id, NULL, "$.nodes"), ".id", ".data.name"))), JSON_EXTRACT(scorecard_data, JSON_UNQUOTE(REPLACE(JSON_SEARCH(`scorecard_data`, "one", scorecard_stack_archive.node_id, NULL, "$.nodes"), ".id", ".data.title")))))
                    ELSE ""
                    END) AS name'),
                'scorecard_stack_archive.company_stack_modules_id',
                'scorecard_stack_archive.company_stack_category_id',
                'scorecard_stack_archive.scorecard_stack_id',
                'scorecard_stack_archive.node_id',
                'scorecard_stack_archive.created_at'
            )
            ->leftjoin('company_stack_category', 'company_stack_category.id', '=', 'scorecard_stack_archive.company_stack_category_id')
            ->leftjoin('scorecard_stack', function ($on) {
                $on->on('scorecard_stack.id', '=', 'scorecard_stack_archive.scorecard_stack_id');
            })
            ->whereNull('scorecard_stack_archive.deleted_at');

        if (!empty($postData['filter_data'])) {
            foreach ($postData['filter_data'] as $key => $value) {
                if (in_array($key, ["type", "name"])) {
                    switch ($key) {
                        case "type":
                            $key = \DB::raw('(CASE
                            WHEN scorecard_stack_archive.type = ' . config('global.SCORECARD_ARCHIVE_TYPE.SCORECARD.id') . ' THEN "' . config('global.SCORECARD_ARCHIVE_TYPE.SCORECARD.name') . '"
                            WHEN scorecard_stack_archive.type = ' . config('global.SCORECARD_ARCHIVE_TYPE.METRIC.id') . ' THEN "' . config('global.SCORECARD_ARCHIVE_TYPE.METRIC.name') . '"
                            ELSE ""
                            END)');
                            break;
                        case "name":
                            $key = \DB::raw('(CASE
                            WHEN scorecard_stack_archive.type = ' . config('global.SCORECARD_ARCHIVE_TYPE.SCORECARD.id') . ' THEN company_stack_category.name
                            WHEN scorecard_stack_archive.type = ' . config('global.SCORECARD_ARCHIVE_TYPE.METRIC.id') . ' THEN JSON_UNQUOTE(IF(JSON_EXTRACT(scorecard_data, JSON_UNQUOTE(REPLACE(JSON_SEARCH(`scorecard_data`, "one", scorecard_stack_archive.node_id, NULL, "$.nodes"), ".id", ".data.name"))) IS NOT NULL, JSON_EXTRACT(scorecard_data, JSON_UNQUOTE(REPLACE(JSON_SEARCH(`scorecard_data`, "one", scorecard_stack_archive.node_id, NULL, "$.nodes"), ".id", ".data.name"))), JSON_EXTRACT(scorecard_data, JSON_UNQUOTE(REPLACE(JSON_SEARCH(`scorecard_data`, "one", scorecard_stack_archive.node_id, NULL, "$.nodes"), ".id", ".data.title")))))
                            ELSE ""
                            END)');
                            break;
                        default:
                            $key = 'scorecard_stack_archive.' . $key;
                            break;
                    }
                    $query = $this->createWhere('text', $key, $value, $query);
                }

                if (in_array($key, ["id"])) {
                    $key   = 'scorecard_stack_archive.' . $key;
                    $query = $this->createWhere('number', $key, $value, $query);
                }
                if (in_array($key, ["created_at"])) {
                    $key   = 'scorecard_stack_archive.' . $key;
                    $query = $this->createWhere('date', $key, $value, $query);
                }
            }
        }
        $orderBy   = 'scorecard_stack_archive.updated_at';
        $orderType = (isset($postData['order_by']) && $postData['order_by'] == 1) ? 'asc' : 'desc';
        if (!empty($postData['sort_data'])) {
            $orderBy   = $postData['sort_data'][0]['col_id'];
            $orderType = $postData['sort_data'][0]['sort'];
        }

        $query       = $query->orderBy($orderBy, $orderType);
        $count       = $query->count();
        $dataPerPage = $query->skip($page)->take($perPage)->get()->toArray();
        return ['data' => $dataPerPage, 'count' => $count];
    }

    public function create($request)
    {
        return ScorecardStackArchive::create([
            'type'                      => $request->type,
            'company_id'                => $request->company_id,
            'project_id'                => $request->project_id,
            'company_stack_modules_id'  => $request->company_stack_modules_id,
            'company_stack_category_id' => $request->company_stack_category_id,
            'scorecard_stack_id'        => $request->scorecard_stack_id,
            'node_id'                   => $request->node_id,
        ]);
    }

    public function details($id, $request)
    {
        $dataDetails = $this->scorecardStackArchive->find($id);
        if (empty($dataDetails)) {
            return null;
        }
        $stack    = CompanyStackModules::select('name')->where('id', $dataDetails->company_stack_modules_id)->first();
        $category = CompanyStackCategory::select('name')->where('id', $dataDetails->company_stack_category_id)->first();
        if ($dataDetails->type == 1) {
            $request = new Request([
                'company_id'                => $dataDetails->company_id,
                'project_id'                => $dataDetails->project_id,
                'company_stack_modules_id'  => $dataDetails->company_stack_modules_id,
                'company_stack_category_id' => $dataDetails->company_stack_category_id,
                'type'                      => $request->type,
            ]);
            $scorecardDetails                = $this->scorecardStackService->details($request);
            $scorecardDetails->stack_name    = $stack->name;
            $scorecardDetails->category_name = $category->name;
            $scorecardDetails->created_at    = $dataDetails->created_at;
            return $scorecardDetails;
        } else {
            $scorecardDetails = [];
            $data = $this->scorecardStackArchive->leftjoin('scorecard_stack', function ($on) {
                $on->on('scorecard_stack.id', '=', 'scorecard_stack_archive.scorecard_stack_id');
            })
                ->select(\DB::raw("JSON_EXTRACT(scorecard_data, JSON_UNQUOTE(REPLACE(JSON_SEARCH(`scorecard_data`, 'one', scorecard_stack_archive.node_id, NULL, '$.nodes'), '.id', ''))) AS nodes"))
                ->where('scorecard_stack_archive.id', $id)->first();
            $node = json_decode($data->nodes, true);
            if (!empty($request->type) && $request->type == "GraphView") {
                $scorecardStackNodeDataList = ScorecardStackNodeData::select('scorecard_stack_node_data.id AS node_edit_id', 'scorecard_stack_node_data.node_id', 'scorecard_stack_node_data.value', 'scorecard_stack_node_data.from_date', 'scorecard_stack_node_data.to_date', 'scorecard_stack_node_data.assigned_color', 'scorecard_stack_node_data.comment')->orderBy('from_date', 'desc')->where('node_id', $dataDetails->node_id)->limit(52)->orderBy('from_date', 'desc');

                $scorecardStackNodeDataList = $scorecardStackNodeDataList->get()->toArray();
                if (sizeof($scorecardStackNodeDataList) > 0) {
                    $node['data']['to_date']      = $scorecardStackNodeDataList[0]['to_date'];
                    $node['data']['from_date']    = $scorecardStackNodeDataList[0]['from_date'];
                    $node['data']['value']        = $scorecardStackNodeDataList[0]['value'];
                    $node['data']['comment']      = $scorecardStackNodeDataList[0]['comment'];
                    $node['data']['node_edit_id'] = $scorecardStackNodeDataList[0]['node_edit_id'];
                    unset($scorecardStackNodeDataList[0]);
                    $node['data']['data_list'] = $scorecardStackNodeDataList;
                } else {
                    $node['data']['to_date']      = null;
                    $node['data']['from_date']    = null;
                    $node['data']['value']        = null;
                    $node['data']['comment']      = null;
                    $node['data']['node_edit_id'] = null;
                    $node['data']['data_list'] = [];
                }
            }
            unset($node['parentNode']);
            unset($node['extent']);
            $node['position']['x'] = 0;
            $node['position']['y'] = 0;
            $node['hidden']                       = false;
            $scorecard_data                     = ['edges' => []];
            $scorecard_data['nodes'][]          = $node;
            $scorecard_data                     = (object) $scorecard_data;
            $scorecard_data                     = json_encode($scorecard_data);
            $scorecardDetails['scorecard_data'] = $scorecard_data;
            $scorecardDetails['stack_name']     = $stack->name;
            $scorecardDetails['category_name']  = $category->name;
            $scorecardDetails['created_at']     = $dataDetails->created_at;
            return $scorecardDetails;
        }
    }

    public function restore($id)
    {
        $dataDetails = $this->scorecardStackArchive->find($id);

        if (empty($dataDetails)) {
            return null;
        }
        if (!empty($dataDetails->node_id)) {
            $query = ScorecardStack::find($dataDetails->scorecard_stack_id);
            $nodeDetails = json_decode($query->scorecard_data, true);
            $newNode = [];
            if (!empty($nodeDetails)) {
                foreach ($nodeDetails['nodes'] as &$nodes) {
                    if ($nodes['id'] == $dataDetails->node_id) {
                        $nodes['hidden'] = false;
                        $newNode = $nodes;
                    }
                }
            }
            $query->update([
                'scorecard_data' => json_encode($nodeDetails),
            ]);
            if (!empty($newNode)) {
                ScorecardStackNodes::where('node_id', $newNode['id'])->update([
                    'node_data' => json_encode($newNode),
                ]);
            }
        }

        return $dataDetails->delete();
    }
}
