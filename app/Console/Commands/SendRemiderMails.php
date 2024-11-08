<?php

namespace App\Console\Commands;

use App\Models\ScorecardStack;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;

class SendRemiderMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder mails to the user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $currentDate = Carbon::now()->subDay();
        $newCurrentDate = Carbon::now();
        $query = ScorecardStack::select(
            'scorecard_stack.id',
            'scorecard_stack.project_id',
            'scorecard_stack.company_stack_category_id',
            'scorecard_stack.scorecard_start_from',
            'scorecard_stack.company_stack_module_id',
            'scorecard_stack.scorecard_type',
            'company_projects.id as projectid',
            'company_projects.name as project_name',
            'scorecard_stack_nodes.id as scorecardstackid',
            'scorecard_stack_nodes.assigned_to',
            'scorecard_stack_nodes.node_data',
            'mst_users.id as users_id',
            'mst_users.first_name',
            'mst_users.last_name',
            'mst_users.email',
            'company_stack_modules.id as stack_modules_id',
            'company_stack_modules.name as module_name',
            'company_stack_category.id as stack_category_id',
            'company_stack_category.name as category_name',
            'mst_users.mobile_no',
            'mst_users.country_code'
        )
            ->leftjoin('company_projects', 'company_projects.id', '=', 'scorecard_stack.project_id')
            ->leftjoin('scorecard_stack_nodes', function ($query) {
                $query->on('scorecard_stack_nodes.scorecard_stack_id', '=', 'scorecard_stack.id');
            })
            ->leftjoin('mst_users', 'mst_users.id', '=', 'scorecard_stack_nodes.assigned_to')
            ->leftjoin('company_stack_modules', 'company_stack_modules.id', '=', 'scorecard_stack.company_stack_module_id')
            ->leftjoin('company_stack_category', 'company_stack_category.id', '=', 'scorecard_stack.company_stack_category_id')
            ->where(function ($where) use ($newCurrentDate) {
                $where->where(function ($in) use ($newCurrentDate) {
                    $in->whereIn('scorecard_stack.scorecard_type', [1, 2]);
                    $in->where('scorecard_stack.scorecard_start_from', $newCurrentDate->dayOfWeek);
                });
                $where->orWhereIn('scorecard_stack.scorecard_type', [4, 5, 6]);
            })
            ->whereNotNull('scorecard_stack_nodes.assigned_to')->get();

        foreach ($query as $dataDetails) {
            $templateData = [];
            $nodeData = $dataDetails->scorecardStackNodeData()->first();
            $lastNodeData = $dataDetails->scorecardStackNodeData()
                ->select(['scorecard_stack_node_data.id', 'scorecard_stack_node_data.scorecard_stack_id', 'scorecard_stack_node_data.node_id', 'scorecard_stack_node_data.value', 'scorecard_stack_node_data.from_date', 'scorecard_stack_node_data.to_date'])
                ->join(\DB::raw('(select max(id) as last_id,scorecard_stack_id,node_id from scorecard_stack_node_data group by scorecard_stack_id,node_id) as latest_data'), 'latest_data.last_id', 'scorecard_stack_node_data.id')
                ->first();
            if (!empty($lastNodeData) && $lastNodeData->to_date == $currentDate->format('Y-m-d')) {
                $nodeName = json_decode($dataDetails->node_data);
                $title = $nodeName->data->name;
                $templateData = [
                    'first_name'    => $dataDetails->first_name,
                    'last_name'     => $dataDetails->last_name,
                    'email'         => $dataDetails->email,
                    'project_name'  => $dataDetails->project_name,
                    'category_name' => $dataDetails->category_name,
                    'title'         => $title,
                    'stack'         => $dataDetails->module_name,
                    'country_code'  => $dataDetails->country_code,
                    'mobile_no'     => $dataDetails->mobile_number,
                ];
            }
            if (empty($nodeData) && ($dataDetails->scorecard_type == 1 || $dataDetails->scorecard_type == 2) || ($dataDetails->scorecard_type == 4 && $newCurrentDate == $newCurrentDate->startOfMonth()) || ($dataDetails->scorecard_type == 5 && $newCurrentDate == $newCurrentDate->startOfQuarter()) || ($dataDetails->scorecard_type == 6 && $newCurrentDate == $newCurrentDate->startOfYear())) {
                $nodeName = json_decode($dataDetails->node_data);
                $title = $nodeName->data->name;
                $templateData = [
                    'first_name'    => $dataDetails->first_name,
                    'last_name'     => $dataDetails->last_name,
                    'email'         => $dataDetails->email,
                    'project_name'  => $dataDetails->project_name,
                    'category_name' => $dataDetails->category_name,
                    'title'         => $title,
                    'stack'         => $dataDetails->module_name,
                    'country_code'  => $dataDetails->country_code,
                    'mobile_no'     => $dataDetails->mobile_number,
                ];
            }
            if (!empty($templateData)) {
                dispatch(new \App\Jobs\SendTemplateEmailJob(config('global.MAIL_TEMPLATE.METRIC_ENTRY_REMINDERS'), $templateData));
            }
        }
    }
}
