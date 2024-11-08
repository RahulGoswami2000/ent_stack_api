<?php

namespace App\Repositories;

use App\Models\CompanyMatrix;
use App\Models\CompanyProject;
use App\Models\CompanyStackCategory;
use App\Models\CompanyStackModules;
use App\Models\GoalStack;
use App\Models\ScorecardStack;
use App\Models\ScorecardStackArchive;
use App\Models\TeamStack;
use App\Models\User;
use App\Models\UserStackAccess;

class CompanyStackCategoryRepository extends BaseRepository
{
    private $projectCategory;

    public function __construct()
    {
        $this->projectCategory = new CompanyStackCategory();
    }

    public function list()
    {
        $query = \DB::table('company_stack_category')
            ->select('company_stack_category.id', 'company_stack_category.name', 'company_stack_category.sequence')
            ->leftjoin('company_projects as cp', 'cp.id', '=', 'company_stack_category.project_id')
            ->leftjoin('mst_company as c', 'c.id', '=', 'company_stack_category.company_id')
            ->whereNull('company_stack_category.deleted_at');

        $data  = $query->get()->toArray();
        $count = $query->count();

        return ['data' => $data, 'count' => $count];
    }

    public function store($request)
    {
        $sequence = 0;
        if (!empty($request->company_id) && !empty($request->project_id)) {
            $query = CompanyStackCategory::select('company_stack_category.sequence')
                ->where('company_stack_category.company_id', $request->company_id)
                ->where('company_stack_category.project_id', $request->project_id)->get();
            $sequence = count($query) + 1;
        }

        $data = CompanyStackCategory::create([
            'company_id'               => $request->company_id,
            'project_id'               => $request->project_id,
            'company_stack_modules_id' => $request->company_stack_modules_id,
            'name'                     => $request->name,
            'sequence'                 => $sequence,
        ]);

        $query = CompanyStackModules::select('company_stack_modules.id as company_stack_modules_id', 'stack_modules_id', 'mst_stack_modules.key')
            ->leftjoin('mst_stack_modules', 'mst_stack_modules.id', '=', 'company_stack_modules.stack_modules_id')
            ->where('company_stack_modules.id', $request->company_stack_modules_id)->first();
        $type = null;
        if ($query->key == 'SCORECARD_STACK') {
            $type = ScorecardStack::class;
            ScorecardStack::create([
                'company_stack_module_id'   => $request->company_stack_modules_id,
                'company_id'                => $request->company_id,
                'project_id'                => $request->project_id,
                'company_stack_category_id' => $data->id,
            ]);

            $stack_table_id = ScorecardStack::where('company_id', $request->company_id)
                ->where('project_id', $request->project_id)
                ->where('company_stack_module_id', $request->company_stack_modules_id)
                ->where('company_stack_category_id', $data->id)->first();
        } else if ($query->key == 'TEAM_STACK') {
            $type = TeamStack::class;
            TeamStack::create([
                'company_id'                => $request->company_id,
                'project_id'                => $request->project_id,
                'company_stack_modules_id'  => $request->company_stack_modules_id,
                'company_stack_category_id' => $data->id,
            ]);

            $stack_table_id = TeamStack::where('company_id', $request->company_id)
                ->where('project_id', $request->project_id)
                ->where('company_stack_modules_id', $request->company_stack_modules_id)
                ->where('company_stack_category_id', $data->id)->first();
        } else if ($query->key == 'GOAL_STACK') {
            $type = GoalStack::class;
            GoalStack::create([
                'company_id'                => $request->company_id,
                'project_id'                => $request->project_id,
                'company_stack_modules_id'  => $request->company_stack_modules_id,
                'company_stack_category_id' => $data->id,
            ]);

            $stack_table_id = GoalStack::where('company_id', $request->company_id)
                ->where('project_id', $request->project_id)
                ->where('company_stack_modules_id', $request->company_stack_modules_id)
                ->where('company_stack_category_id', $data->id)->first();
        }
        $user_type = User::select('user_type')->where('id', \Auth::user()->id)->first();
        $role_type = CompanyMatrix::select('role_id')->where('user_id', \Auth::user()->id)->where('company_id', $request->company_id)->first();
        if ($user_type->user_type == '2') {
            if (!empty($stack_table_id)) {
                if ($role_type->role_id != 2) {
                    $dataDetails = CompanyProject::select('company_projects.id as project_id', 'company_projects.company_id', 'mst_company.id as company_id', 'mst_company.user_id')
                        ->leftjoin('mst_company', 'mst_company.id', '=', 'company_projects.company_id')
                        ->where('mst_company.id', 'company_projects.company_id')
                        ->where('company_projects.id', $request->project_id)->first();
                    if (!empty($dataDetails)) {
                        UserStackAccess::create([
                            'user_id'                   => $dataDetails->user_id,
                            'company_id'                => $dataDetails->company_id,
                            'project_id'                => $request->project_id,
                            'company_stack_modules_id'  => $request->company_stack_modules_id,
                            'company_stack_category_id' => $data->id,
                            'stack_table_id'            => $stack_table_id->id,
                            'stack_table_type'          => $type,
                        ]);
                    }
                }
                $data = UserStackAccess::create([
                    'user_id'                   => \Auth::user()->id,
                    'company_id'                => $request->company_id,
                    'project_id'                => $request->project_id,
                    'company_stack_modules_id'  => $request->company_stack_modules_id,
                    'company_stack_category_id' => $data->id,
                    'stack_table_id'            => $stack_table_id->id,
                    'stack_table_type'          => $type,
                ]);
            }
        }

        return $data;
    }

    public function details($id)
    {
        $data = $this->projectCategory->find($id);

        if (empty($data)) {
            return null;
        }
        return $data;
    }

    public function update($id, $request)
    {
        $data = $this->projectCategory->find($id);
        $data->update([
            'name' => $request->name,
        ]);
        return $data;
    }

    public function delete($id)
    {
        TeamStack::where('company_stack_category_id', $id)->delete();
        ScorecardStack::where('company_stack_category_id', $id)->delete();
        GoalStack::where('company_stack_category_id', $id)->delete();

        return $this->projectCategory->find($id)->delete();
    }

    public function updateSequence($request)
    {
        $projectCount = $request->project;
        foreach ($projectCount as $key => $value) {
            $query = CompanyStackCategory::whereIn('id', [json_decode(json_encode($request->project[$key]))->id])
                ->update(['sequence' => json_decode(json_encode($request->project[$key]))->sequence]);
        }
        return $query;
    }

    public function duplicateCategory($request)
    {
        $query = CompanyStackModules::select('company_stack_modules.id as company_stack_modules_id', 'stack_modules_id', 'mst_stack_modules.key')
            ->leftjoin('mst_stack_modules', 'mst_stack_modules.id', '=', 'company_stack_modules.stack_modules_id')
            ->where('company_stack_modules.id', $request->company_stack_modules_id)->first();
        $lastSequence = \DB::table('company_stack_category')->latest('id')->first();
        $sequence = $lastSequence->sequence + 1;
        $stackCategory = CompanyStackCategory::create([
            'company_id'               => $request->company_id,
            'project_id'               => $request->project_id,
            'company_stack_modules_id' => $request->company_stack_modules_id,
            'name'                     => $request->name . ' (Copy)',
            'sequence'                 => $sequence,
        ]);
        $type = null;
        if ($query->key == 'SCORECARD_STACK') {
            $type = ScorecardStack::class;
            $oldStackDetail = ScorecardStack::where('company_id', $request->company_id)
                ->where('project_id', $request->project_id)
                ->where('company_stack_module_id', $request->company_stack_modules_id)
                ->where('company_stack_category_id', $request->company_stack_category_id)->first();

            $stack_table_id = ScorecardStack::create([
                'company_stack_module_id'   => $request->company_stack_modules_id,
                'company_id'                => $request->company_id,
                'project_id'                => $request->project_id,
                'company_stack_category_id' => $stackCategory->id,
                'scorecard_data'            => $oldStackDetail->scorecard_data,
            ]);
        } else if ($query->key == 'TEAM_STACK') {
            $oldStackDetail = TeamStack::where('company_id', $request->company_id)
                ->where('project_id', $request->project_id)
                ->where('company_stack_modules_id', $request->company_stack_modules_id)
                ->where('company_stack_category_id', $request->company_stack_category_id)->first();

            $stack_table_id = TeamStack::create([
                'company_id'                => $request->company_id,
                'project_id'                => $request->project_id,
                'company_stack_modules_id'  => $request->company_stack_modules_id,
                'company_stack_category_id' => $stackCategory->id,
                'team_stack_data'           => $oldStackDetail->team_stack_data,
            ]);
            $type = TeamStack::class;
        } else if ($query->key == 'GOAL_STACK') {
            $oldStackDetail = GoalStack::where('company_id', $request->company_id)
                ->where('project_id', $request->project_id)
                ->where('company_stack_modules_id', $request->company_stack_modules_id)
                ->where('company_stack_category_id', $request->company_stack_category_id)->first();

            $stack_table_id = GoalStack::create([
                'company_id'                => $request->company_id,
                'project_id'                => $request->project_id,
                'company_stack_modules_id'  => $request->company_stack_modules_id,
                'company_stack_category_id' => $stackCategory->id,
                'stack_data'                => $oldStackDetail->stack_data,
            ]);
            $type = GoalStack::class;
        }
        $user_type = User::select('user_type')->where('id', \Auth::user()->id)->first();
        $role_type = CompanyMatrix::select('role_id')->where('user_id', \Auth::user()->id)->where('company_id', $request->company_id)->first();
        if ($user_type->user_type == '2') {
            if (!empty($stack_table_id)) {
                if ($role_type->role_id != 2) {
                    $dataDetails = CompanyProject::select('company_projects.id as project_id', 'company_projects.company_id', 'mst_company.id as company_id', 'mst_company.user_id')
                        ->leftjoin('mst_company', 'mst_company.id', '=', 'company_projects.company_id')
                        ->where('mst_company.id', 'company_projects.company_id')
                        ->where('company_projects.id', $request->project_id)->first();
                    if (!empty($dataDetails)) {
                        UserStackAccess::create([
                            'user_id'                   => $dataDetails->user_id,
                            'company_id'                => $dataDetails->company_id,
                            'project_id'                => $request->project_id,
                            'company_stack_modules_id'  => $request->company_stack_modules_id,
                            'company_stack_category_id' => $stackCategory->id,
                            'stack_table_id'            => $stack_table_id->id,
                            'stack_table_type'          => $type,
                        ]);
                    }
                }
                $data = UserStackAccess::create([
                    'user_id'                   => \Auth::user()->id,
                    'company_id'                => $request->company_id,
                    'project_id'                => $request->project_id,
                    'company_stack_modules_id'  => $request->company_stack_modules_id,
                    'company_stack_category_id' => $stackCategory->id,
                    'stack_table_id'            => $stack_table_id->id,
                    'stack_table_type'          => $type,
                ]);
            }
        }
        $data = ['stackCategory' => $stackCategory];

        return $data;
    }

    public function bulkUpdate($request)
    {
        $data = $request->all();

        foreach ($data['data'] as $dataDetails) {
            $query = CompanyStackCategory::find($dataDetails['category_id']);
            if ($dataDetails['is_deleted'] == 1) {
                TeamStack::where('company_stack_category_id', $dataDetails['category_id'])->delete();
                ScorecardStack::where('company_stack_category_id', $dataDetails['category_id'])->delete();
                GoalStack::where('company_stack_category_id', $dataDetails['category_id'])->delete();
                ScorecardStackArchive::where('company_stack_category_id', $dataDetails['category_id'])->delete();
                $query->delete();
            } else {
                $updateData['name'] = $dataDetails['name'];
                if (!empty($dataDetails['sequence'])) {
                    $updateData['sequence'] = $dataDetails['sequence'];
                }

                $query->update($updateData);
            }
        }
        return $query;
    }
}
