<?php

namespace App\Repositories;

use App\Library\FunctionUtils;
use App\Models\GoalStack;
use App\Models\User;

class GoalStackRepository extends BaseRepository
{
    private $goalStack;

    public function __construct()
    {
        $this->goalStack = new GoalStack();
    }

    public function list()
    {
        $query = \DB::table('goal_stack')
            ->select('goal_stack.id', 'goal_stack.company_id', 'goal_stack.project_id', 'goal_stack.company_stack_modules_id', 'goal_stack.company_stack_category_id', 'goal_stack.stack_data')
            ->whereNull('goal_stack.deleted_at');
        $data = $query->get()->toArray();
        $count = $query->count();
        return ['data' => $data, 'count' => $count];
    }

    public function details($request)
    {
        $data = (is_array($request) || is_object($request)) ? $this->dataDetails($request) : $this->goalStack->find($request);

        if (empty($data)) {
            return null;
        }

        $data->userAccess = $data->userAccess()->select(['users.id', \DB::raw("CONCAT(users.first_name,' ',users.last_name) as name"), \DB::raw("IF (users.profile_image IS NOT NULL, " . \DB::raw("CONCAT('" . FunctionUtils::getS3Url(config('global.UPLOAD_PATHS.USER_PROFILE')) . "', users.profile_image)") . ', NULL) AS profile_image'), 'users.email'])
            ->leftjoin('mst_users as users', 'users.id', '=', 'user_id')
            ->get();
        $goalStackData = json_decode($data->stack_data, true);
        $allNodesData  = $goalStackData['nodes'] ?? [];
        $userIds       = array_filter(\Arr::pluck($allNodesData, 'data.user.id'), function ($item) {
            return !empty($item);
        });
        $userData      = User::whereIn('id', $userIds)->get()->toArray();

        for ($i = 0; $i < sizeof($allNodesData); $i++) {
            $nodeData = $allNodesData[$i]['data'];
            $profileImage = null;
            if (!empty($nodeData['user']) && !empty($nodeData['user']['id'])) {
                $userNodeKey  = array_search($nodeData['user']['id'], array_column($userData, 'id'));
                $profileImage = $userNodeKey !== false ? $userData[$userNodeKey]['profile_image'] : null;
                $userImage    = $profileImage ? FunctionUtils::getS3FileUrl(config('global.UPLOAD_PATHS.USER_PROFILE')  . $profileImage) : null;

                $userNodeKey !== false && $nodeData['avatar'] = $userImage;
                $userNodeKey !== false && $nodeData['user']['profile_image'] = $userImage;
                $userNodeKey !== false && $nodeData['user']['name'] = $userData[$userNodeKey]['first_name'] . " " . $userData[$userNodeKey]['last_name'];
                $userNodeKey !== false && $nodeData['user']['job_role'] = $userData[$userNodeKey]['job_role'];
                $userNodeKey !== false && $nodeData['user']['email'] = $userData[$userNodeKey]['email'];
            }
            $allNodesData[$i]['data'] = $nodeData;
        }
        $data->temp_node = $allNodesData;

        $data->stack_data = json_encode(['edges' => $goalStackData['edges'] ?? [], 'nodes' => $allNodesData]);

        $this->goalStack->find($data->id)->update([
            'stack_data' => $data->stack_data,
        ]);

        return $data;
    }

    public function delete($id)
    {
        return $this->goalStack->find($id)->delete();
    }

    public function save($request)
    {
        $query = GoalStack::where('company_id', $request->company_id)
            ->where('project_id', $request->project_id)
            ->where('company_stack_modules_id', $request->company_stack_modules_id)
            ->where('company_stack_category_id', $request->company_stack_category_id)->first();
        if (empty($query)) {
            $data = GoalStack::create([
                'company_id'                => $request->company_id,
                'project_id'                => $request->project_id,
                'company_stack_modules_id'  => $request->company_stack_modules_id,
                'company_stack_category_id' => $request->company_stack_category_id
            ]);
        } else {
            $data = $query->update([
                'stack_data' => $request->stack_data,
            ]);
        }

        return $data;
    }

    public function dataDetails($request)
    {
        $query = GoalStack::where('company_id', $request->company_id)
            ->where('project_id', $request->project_id)
            ->where('company_stack_modules_id', $request->company_stack_modules_id)
            ->where('company_stack_category_id', $request->company_stack_category_id)->first();

        if (empty($query)) {
            return null;
        }
        return $query;
    }
}
