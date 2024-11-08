<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\CompanyStackCategory;
use App\Models\CompanyStackModules;
use App\Models\GoalStack;
use App\Models\ScorecardStack;
use App\Models\ScorecardStackArchive;
use App\Models\TeamStack;
use App\Models\User;
use App\Models\UserStackAccess;

class CompanyStackModuleRepository extends BaseRepository
{
    private $companyStackModule;

    public function __construct()
    {
        $this->companyStackModule = new CompanyStackModules();
    }

    /**
     * Store Company Stack Module
     */
    public function store($request)
    {
        $sequence = 0;

        if (!empty($request->company_id) && !empty($request->project_id)) {
            $query = CompanyStackModules::select('company_stack_modules.sequence')
                ->where('company_stack_modules.company_id', $request->company_id)
                ->where('company_stack_modules.project_id', $request->project_id)->get();
            $sequence = count($query) + 1;
        }

        return CompanyStackModules::create([
            'company_id'       => $request->company_id,
            'project_id'       => $request->project_id,
            'name'             => $request->name,
            'stack_modules_id' => $request->stack_modules_id,
            'sequence'         => $sequence,
        ]);
    }
    /**
     * Details Company Stack Module
     */
    public function detail($id)
    {
        $dataDetails = $this->companyStackModule->find($id);

        if (empty($dataDetails)) {
            return null;
        }

        return $dataDetails;
    }
    /**
     * Update Company Stack Module
     */
    public function update($id, $request)
    {
        $data = $this->companyStackModule->find($id);
        $data->update([
            'company_id'       => $request->company_id,
            'project_id'       => $request->project_id,
            'name'             => $request->name,
            'stack_modules_id' => $request->stack_modules_id,
        ]);
        return $data;
    }
    /**
     * Delete Company Stack Module
     */
    public function destroy($id)
    {
        CompanyStackCategory::where('company_stack_modules_id', $id)->delete();
        TeamStack::where('company_stack_modules_id', $id)->delete();
        ScorecardStack::where('company_stack_module_id', $id)->delete();
        GoalStack::where('company_stack_modules_id', $id)->delete();

        return $this->companyStackModule->find($id)->delete();
    }
    /**
     * Change Status Company Stack Module
     */
    public function changeStatus($id, $request)
    {
        $data = $this->companyStackModule->find($id);
        $data->update([
            'is_active' => $request->is_active,
        ]);

        return $data;
    }

    public function bulkUpdate($request)
    {
        $data = $request->all();
        foreach ($data['data'] as $dataDetails) {
            $query = CompanyStackModules::find($dataDetails['company_stack_modules_id']);
            if ($dataDetails['is_deleted'] == 1) {
                CompanyStackCategory::where('company_stack_modules_id', $dataDetails['company_stack_modules_id'])->delete();
                TeamStack::where('company_stack_modules_id', $dataDetails['company_stack_modules_id'])->delete();
                ScorecardStack::where('company_stack_module_id', $dataDetails['company_stack_modules_id'])->delete();
                GoalStack::where('company_stack_modules_id', $dataDetails['company_stack_modules_id'])->delete();
                ScorecardStackArchive::where('company_stack_modules_id', $dataDetails['company_stack_modules_id'])->delete();
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

    public function duplicateStack($request)
    {
        $query = CompanyStackModules::select('company_stack_modules.id as company_stack_modules_id', 'stack_modules_id', 'mst_stack_modules.key')
            ->leftjoin('mst_stack_modules', 'mst_stack_modules.id', '=', 'company_stack_modules.stack_modules_id')
            ->where('company_stack_modules.id', $request->company_stack_modules_id)->first();
        $lastSequence = CompanyStackModules::select('sequence')->where('company_id', $request->company_id)->where('project_id', $request->project_id)->latest('id')->first();
        $sequence     = $lastSequence->sequence + 1;
        $stackModule  = CompanyStackModules::create([
            'stack_modules_id' => $query->stack_modules_id,
            'company_id'       => $request->company_id,
            'project_id'       => $request->project_id,
            'name'             => $request->name . ' (Copy)',
            'sequence'         => $sequence,
        ]);
        $categoryData = CompanyStackCategory::where('company_stack_modules_id', $request->company_stack_modules_id)->where('is_active', 1)->whereNull('deleted_at')->get();
        foreach ($categoryData as $data) {
            $categorySequence = $data->sequence;
            $categorySequence += 1;
            $stackCategory = CompanyStackCategory::create([
                'company_id'               => $data->company_id,
                'project_id'               => $data->project_id,
                'company_stack_modules_id' => $stackModule->id,
                'name'                     => $data->name . ' (Copy)',
                'sequence'                 => $categorySequence,
            ]);
            if ($query->key == 'SCORECARD_STACK') {
                $type = ScorecardStack::class;
                $oldStackDetail = ScorecardStack::where('company_id', $request->company_id)
                    ->where('project_id', $request->project_id)
                    ->where('company_stack_module_id', $request->company_stack_modules_id)
                    ->where('company_stack_category_id', $data->id)->first();

                $stack_table_id = ScorecardStack::create([
                    'company_stack_module_id'   => $stackModule->id,
                    'company_id'                => $request->company_id,
                    'project_id'                => $request->project_id,
                    'company_stack_category_id' => $stackCategory->id,
                    'scorecard_data'            => $oldStackDetail->scorecard_data,
                ]);
            } else if ($query->key == 'TEAM_STACK') {
                $oldStackDetail = TeamStack::where('company_id', $request->company_id)
                    ->where('project_id', $request->project_id)
                    ->where('company_stack_modules_id', $request->company_stack_modules_id)
                    ->where('company_stack_category_id', $data->id)->first();

                $stack_table_id = TeamStack::create([
                    'company_id'                => $request->company_id,
                    'project_id'                => $request->project_id,
                    'company_stack_modules_id'  => $stackModule->id,
                    'company_stack_category_id' => $stackCategory->id,
                    'team_stack_data'           => $oldStackDetail->team_stack_data,
                ]);
                $type = TeamStack::class;
            } else if ($query->key == 'GOAL_STACK') {
                $oldStackDetail = GoalStack::where('company_id', $request->company_id)
                    ->where('project_id', $request->project_id)
                    ->where('company_stack_modules_id', $request->company_stack_modules_id)
                    ->where('company_stack_category_id', $data->id)->first();

                $stack_table_id = GoalStack::create([
                    'company_id'                => $request->company_id,
                    'project_id'                => $request->project_id,
                    'company_stack_modules_id'  => $stackModule->id,
                    'company_stack_category_id' => $stackCategory->id,
                    'stack_data'                => $oldStackDetail->stack_data,
                ]);
                $type = GoalStack::class;
            }
            if (!empty($stack_table_id)) {
                $data = UserStackAccess::create([
                    'user_id'                   => \Auth::user()->id,
                    'company_id'                => $request->company_id,
                    'project_id'                => $request->project_id,
                    'company_stack_modules_id'  => $stackModule->id,
                    'company_stack_category_id' => $stackCategory->id,
                    'stack_table_id'            => $stack_table_id->id,
                    'stack_table_type'          => $type,
                ]);
            }
        }
        $data = ['stackModule' => $stackModule];
        return $data;
    }
}
