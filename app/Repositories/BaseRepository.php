<?php


namespace App\Repositories;

use App\Models\CompanyStackModules;
use App\Models\GoalStack;
use App\Models\ScorecardStack;
use App\Models\Stack;
use App\Models\TeamStack;
use App\Models\UserStackAccess;

class BaseRepository
{

    public function assignPermissions($user_id, $company_id, $project_id, $company_stack_modules_id, $company_stack_category_id)
    {
        $stack = CompanyStackModules::select('company_stack_modules.id', 'company_stack_modules.stack_modules_id', 'mst_stack_modules.key')
            ->where('company_stack_modules.id', $company_stack_modules_id)
            ->leftjoin('mst_stack_modules', 'mst_stack_modules.id', '=', 'company_stack_modules.stack_modules_id')
            ->first();

        $type = null;
        $data = null;
        if ($stack->key == 'SCORECARD_STACK') {
            $type           = ScorecardStack::class;
            $stack_table_id = ScorecardStack::where('company_id', $company_id)
                ->where('project_id', $project_id)
                ->where('company_stack_module_id', $company_stack_modules_id)
                ->where('company_stack_category_id', $company_stack_category_id)->first();
        } else if ($stack->key == 'TEAM_STACK') {
            $type           = TeamStack::class;
            $stack_table_id = TeamStack::where('company_id', $company_id)
                ->where('project_id', $project_id)
                ->where('company_stack_modules_id', $company_stack_modules_id)
                ->where('company_stack_category_id', $company_stack_category_id)->first();
        } else if ($stack->key == 'GOAL_STACK') {
            $type           = GoalStack::class;
            $stack_table_id = GoalStack::where('company_id', $company_id)
                ->where('project_id', $project_id)
                ->where('company_stack_modules_id', $company_stack_modules_id)
                ->where('company_stack_category_id', $company_stack_category_id)->first();
        }

        if (!empty($stack_table_id)) {
            if (is_array($user_id)) {
                foreach ($user_id as $user) {
                    $data = UserStackAccess::create([
                        'user_id'                   => $user,
                        'company_id'                => $company_id,
                        'project_id'                => $project_id,
                        'company_stack_modules_id'  => $company_stack_modules_id,
                        'company_stack_category_id' => $company_stack_category_id,
                        'stack_table_id'            => $stack_table_id->id,
                        'stack_table_type'          => $type,
                    ]);
                }
            } else {
                $data = UserStackAccess::create([
                    'user_id'                   => $user_id,
                    'company_id'                => $company_id,
                    'project_id'                => $project_id,
                    'company_stack_modules_id'  => $company_stack_modules_id,
                    'company_stack_category_id' => $company_stack_category_id,
                    'stack_table_id'            => $stack_table_id->id,
                    'stack_table_type'          => $type,
                ]);
            }
        }
        return $data;
    }
}
