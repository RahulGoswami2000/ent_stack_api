<?php

namespace App\Repositories;

use App\Library\FunctionUtils;
use App\Models\Company;
use App\Models\TeamStack;
use App\Models\User;
use App\Models\UserStackAccess;
use function Symfony\Component\Finder\filter;

class TeamStackRepository extends BaseRepository
{
    private $teamStack;
    private $company;
    private $user;

    public function __construct()
    {
        $this->teamStack = new TeamStack();
        $this->company   = new Company();
        $this->user      = new User();
    }

    /**
     * List Team Stack
     */
    public function list()
    {
        $query = \DB::table('team_stack')
            ->select(
                'team_stack.id',
                'team_stack.company_id',
                'team_stack.project_id',
                'team_stack.project_category_id',
                'team_stack.team_stack_data',
                \DB::raw('IF(`team_stack`.`is_active` = 1,"' . __('labels.active') . '","' . __('labels.inactive') . '") AS display_status')
            )
            ->whereNull('team_stack.deleted_at');
        $data  = $query->get()->toArray();
        $count = $query->count();
        return ['data' => $data, 'count' => $count];
    }

    /**
     * Store Team Stack
     */
    public function store($request)
    {
        return TeamStack::create([
            'company_id'          => $request->company_id,
            'project_id'          => $request->project_id,
            'project_category_id' => $request->project_category_id,
            'team_stack_data'     => json_encode($request->team_stack_data),
        ]);
    }

    /**
     * Details Team Stack
     */
    public function details($request)
    {
        $data = (is_array($request) || is_object($request)) ? $this->dataDetails($request) : $this->teamStack->find($request);

        if (empty($data)) {
            return null;
        }

        $data->userAccess = $data->userAccess()->select(['users.id', \DB::raw("CONCAT(users.first_name,' ',users.last_name) as name"), \DB::raw("IF (users.profile_image IS NOT NULL, " . \DB::raw("CONCAT('" . FunctionUtils::getS3Url(config('global.UPLOAD_PATHS.USER_PROFILE'))  . "', users.profile_image)") . ', NULL) AS profile_image'), 'users.email'])
            ->leftjoin('mst_users as users', 'users.id', '=', 'user_id')
            ->get();

        $teamStackData = json_decode($data->team_stack_data, true);
        $allNodesData  = $teamStackData['nodes'] ?? [];
        $userIds       = array_filter(\Arr::pluck($allNodesData, 'data.user.id'), function ($item) {
            return !empty($item);
        });
        $userData      = User::whereIn('id', $userIds)->get()->toArray();

        for ($i = 0; $i < sizeof($allNodesData); $i++) {
            $nodeData = $allNodesData[$i]['data'];
            if (!empty($nodeData['user']) && !empty($nodeData['user']['id'])) {
                $userNodeKey  = array_search($nodeData['user']['id'], array_column($userData, 'id'));
                $profileImage = $userNodeKey !== false ? $userData[$userNodeKey]['profile_image'] : null;

                $userNodeKey !== false && $nodeData['user']['profile_image'] = $profileImage ? FunctionUtils::getS3FileUrl(config('global.UPLOAD_PATHS.USER_PROFILE') . $profileImage) : null;
                $userNodeKey !== false && $nodeData['user']['name'] = $userData[$userNodeKey]['first_name'] . " " . $userData[$userNodeKey]['last_name'];
                $userNodeKey !== false && $nodeData['user']['job_role'] = $userData[$userNodeKey]['job_role'];
                $userNodeKey !== false && $nodeData['user']['email'] = $userData[$userNodeKey]['email'];
            }
            $allNodesData[$i]['data'] = $nodeData;
        }
        $data->temp_node = $allNodesData;

        $data->team_stack_data = json_encode(['edges' => $teamStackData['edges'] ?? [], 'nodes' => $allNodesData]);

        $this->teamStack->find($data->id)->update([
            'team_stack_data'     => $data->team_stack_data,
        ]);

        return $data;
    }

    /**
     * Details Team Stack
     */
    public function companyDetails($id)
    {
        $dataDetails = $this->company->where('user_id', $id)->get();

        if (empty($dataDetails)) {
            return null;
        }

        return $dataDetails;
    }

    /**
     * Update Team Stack
     */
    public function update($id, $request)
    {
        $data = $this->teamStack->find($id);
        $data->update([
            'company_id'          => $request->company_id,
            'project_id'          => $request->project_id,
            'project_category_id' => $request->project_category_id,
            'scorecard_type'      => $request->scorecard_type,
            'team_stack_data'     => json_encode($request->team_stack_data),
        ]);
        return $data;
    }

    /**
     * Delete Team Stack
     */
    public function destroy($id)
    {
        return $this->teamStack->find($id)->delete();
    }

    public function updateRole($id, $request)
    {
        $userID = $this->company->where('user_id', $id)->pluck('user_id')->first();
        if (empty($userID)) {
            return null;
        }
        $data = $this->user->find($userID);
        $data->update([
            'role_id' => $request->role_id
        ]);
        return $data;
    }

    public function save($request)
    {
        $query = TeamStack::where('company_id', $request->company_id)
            ->where('project_id', $request->project_id)
            ->where('company_stack_modules_id', $request->company_stack_modules_id)
            ->where('company_stack_category_id', $request->company_stack_category_id)->first();

        if (empty($query)) {
            $data = TeamStack::create([
                'company_id'                => $request->company_id,
                'project_id'                => $request->project_id,
                'company_stack_modules_id'  => $request->company_stack_modules_id,
                'company_stack_category_id' => $request->company_stack_category_id,
                'team_stack_data'           => $request->team_stack_data,
            ]);
        } else {
            $data = $this->dataDetails($request);
            $data->update([
                'team_stack_data' => $request->team_stack_data,
            ]);
        }

        return $data;
    }

    public function dataDetails($request)
    {
        $query = TeamStack::where('company_id', $request->company_id)
            ->where('project_id', $request->project_id)
            ->where('company_stack_modules_id', $request->company_stack_modules_id)
            ->where('company_stack_category_id', $request->company_stack_category_id)->first();

        if (empty($query)) {
            return null;
        }
        return $query;
    }
}
