<?php

namespace App\Repositories;

use App\Models\CompanyProject;
use App\Models\CompanyStackCategory;
use App\Models\CompanyStackModules;
use App\Models\ScorecardStack;
use App\Models\ScorecardStackArchive;
use App\Models\TeamStack;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Request;

class CompanyProjectRepository extends BaseRepository
{
    private $companyProject;

    public function __construct()
    {
        $this->companyProject = new CompanyProject;
    }

    /**
     * List Company Project
     */
    public function list()
    {
        $query = \DB::table('company_projects')
            ->select(
                'company_projects.id',
                'company_projects.company_id',
                'company_projects.name',
                'company_projects.sequence',
                \DB::raw('IF(`company_projects`.`is_active` = 1,"' .  __('labels.active')  . '","' .  __('labels.inactive')  . '") AS display_status')
            )
            ->whereNull('company_projects.deleted_at');
        $data  = $query->get()->toArray();
        $count = $query->count();
        // print_r($count);
        // die;
        return ['data' => $data, 'count' => $count];
    }
    /**
     * Store Company Project
     */
    public function store($request)
    {
        $sequence = 0;

        if (!empty($request->company_id)) {
            $query = CompanyProject::select('company_projects.sequence')
                ->where('company_projects.company_id', $request->company_id)->get();
            $sequence = count($query) + 1;
        }

        $project = CompanyProject::create([
            'company_id' => $request->company_id,
            'name'       => $request->name,
            'sequence'   => $sequence,
        ]);

        $stackModules = \DB::table('mst_stack_modules')
            ->select('id', 'name')
            ->whereNull('deleted_at')
            ->get();

        $updateSequence = 0;
        if (!empty($request->company_id) && !empty($project->id)) {
            $query = CompanyStackModules::select('company_stack_modules.sequence')
                ->where('company_stack_modules.company_id', $request->company_id)
                ->where('company_stack_modules.project_id', $project->id)->get();
            $updateSequence = count($query) + 1;
        }

        foreach ($stackModules as $stackModule) {
            $companyStackModule = CompanyStackModules::create([
                'stack_modules_id' => $stackModule->id,
                'company_id'       => $request->company_id,
                'project_id'       => $project->id,
                'name'             => $stackModule->name,
                'sequence'         => $updateSequence
            ]);
        }

        $data = ['Project' => $project, 'Company Stack Module' => $companyStackModule];

        return $data;
    }
    /**
     * Details Company Project
     */
    public function details($id)
    {
        $dataDetails = $this->companyProject->find($id);

        if (empty($dataDetails)) {
            return null;
        }

        return $dataDetails;
    }
    /**
     * Update Company Project
     */
    public function update($id, $request)
    {
        $data = $this->companyProject->find($id);
        $data->update([
            'company_id' => $request->company_id,
            'name'       => $request->name,
        ]);
        return $data;
    }
    /**
     * Delete Company Project
     */
    public function destroy($id)
    {
        CompanyStackCategory::where('project_id', $id)->delete();
        CompanyStackModules::where('project_id', $id)->delete();
        ScorecardStack::where('project_id', $id)->delete();
        TeamStack::where('project_id', $id)->delete();
        ScorecardStackArchive::where('project_id', $id)->delete();
        return $this->companyProject->find($id)->delete();
    }
    /**
     * Change Status Company Project
     */
    public function changeStatus($id, $request)
    {
        $data = $this->companyProject->find($id);
        $data->update([
            'is_active' => $request->is_active,
        ]);

        return $data;
    }

    public function bulkUpdate($request)
    {
        $data = $request->all();
        foreach ($data['data'] as $dataDetails) {
            $query = CompanyProject::find($dataDetails['project_id']);
            if ($dataDetails['is_deleted'] == 1) {
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
