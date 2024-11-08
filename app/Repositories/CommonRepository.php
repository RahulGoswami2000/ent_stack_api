<?php

namespace App\Repositories;

use App\Models\LovPrivileges;
use App\Models\Metric;
use App\Models\MetricGroup;
use App\Traits\CommonTrait;

class CommonRepository extends BaseRepository
{
    use CommonTrait;

    public function __construct()
    {
    }

    public function metric($postData, $page = 1, $perPage = 100)
    {
        $query = \DB::table('metric')
            ->select('metric.id', 'metric.name', 'metric_categories.id as metric_category_id', 'metric_categories.name as metric_category_name', 'metric.type', 'metric.format_of_matrix', 'metric.expression', 'metric.expression_ids', 'metric.expression_readable', 'metric.expression_data', \DB::raw('(CASE
            WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.DOLLAR.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.DOLLAR.name') . '"
            WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.PERCENTAGE.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.PERCENTAGE.name') . '"
            WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.QTY.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.QTY.name') . '"
            ELSE "N/A"
            END) AS display_format_of_matrix'))
            ->whereNull('metric.deleted_at')
            ->where('metric.is_active', 1)
            ->leftjoin('metric_categories', 'metric_categories.id', '=', 'metric.metric_category_id');
        if ($postData->access_type == 0) {
            if ($postData->company_id) {
                $query->where(function ($where) use ($postData) {
                    $where->where('metric.company_id', $postData->company_id);
                    $where->orWhere('metric.is_admin', 1);
                });
            }
        } else if (isset($postData->access_type) == 1) {
            $query->where('metric.is_admin', 1);
        }

        if ((!empty($postData->type) && $postData->type == 1) || (isset($postData->metric_category_id) && $postData->metric_category_id != -1)) {
            $query->where('metric.metric_category_id', $postData->metric_category_id);
        }

        if ((empty($postData->type) && $postData->type == 2) || ($postData->has('metric_category_id') && empty($postData->metric_category_id))) {
            $query->whereNull('metric.metric_category_id');
            $query->where('metric.is_admin','!=', 1);
        }

        if (!empty($postData->metric_type)) {
            $query->where('metric.type', $postData->metric_type);
        }

        if (!empty($postData->search)) {
            $query->where(function ($where) use ($postData) {
                $where->where('metric.name', 'like', '%' . $postData->search . '%');
            });
        }

        $query       = $query->orderBy('metric.name');
        $count       = $query->count();
        $dataPerPage = $query->skip($page - 1)->take($perPage)->get()->toArray();
        return ['data' => $dataPerPage, 'count' => $count];
    }

    public function metricGroup($postData, $page = 1, $perPage = 100)
    {
        $query = \DB::table('metric_groups')
            ->select('metric_groups.id', 'metric_groups.name', 'metric_categories.id as metric_category_id', 'metric_categories.name as metric_category_name')
            ->whereNull('metric_groups.deleted_at')
            ->where('metric_groups.is_active', 1)
            ->leftjoin('metric_categories', 'metric_categories.id', '=', 'metric_groups.metric_category_id')
            ->where('metric_categories.is_active', 1);
        if (isset($postData['type']) && $postData['type'] == 1) {
            $query->where('metric_groups.metric_category_id', $postData['metric_category_id']);
        }
        if (isset($postData['type']) && $postData['type'] == 2) {
            $query->whereNull('metric_groups.metric_category_id');
        }

        if (!empty($postData['search'])) {
            $query->where('metric_groups.name', 'like', '%' . $postData['search'] . '%');
        }

        $query       = $query->orderBy('metric_groups.name');
        $count       = $query->count();
        $dataPerPage = $query->skip($page - 1)->take($perPage)->get()->toArray();
        return ['data' => $dataPerPage, 'count' => $count];
    }

    public function category($postData, $page = 1, $perPage = 100)
    {
        $metric = \DB::table('metric')
            ->select('metric.id', 'metric.name', 'metric.metric_category_id')
            ->whereNull('metric.deleted_at')
            ->where('metric.name', 'like',  '%' . $postData->search . '%')
            ->where('metric.is_active', 1)->get()->pluck('metric_category_id')->toArray();
        $metricGroups = \DB::table('metric_groups')
            ->select('metric_groups.id', 'metric_groups.name', 'metric_groups.metric_category_id')
            ->whereNull('metric_groups.deleted_at')
            ->where('metric_groups.name', 'like',  '%' . $postData->search . '%')
            ->where('metric_groups.is_active', 1)->get()->pluck('metric_category_id')->toArray();

        $metricCategoryId = array_unique(array_merge($metric, $metricGroups));

        $query = \DB::table('metric_categories')
            ->select('metric_categories.id', 'metric_categories.name')
            ->whereNull('metric_categories.deleted_at')
            ->where('metric_categories.is_active', 1);

        if ($postData->has('search') && !empty($postData->search)) {
            //            $query = $query->leftJoinSub($metric, 'metric', function ($join) {
            //                $join->on('metric_categories.id', '=', 'metric.metric_category_id');
            //            })->leftJoinSub($metricGroups, 'metric_groups', function ($join) {
            //                $join->on('metric_categories.id', '=', 'metric_groups.metric_category_id');
            //            });
            //            $query = $query->where(function ($query) use ($postData) {
            //                $query->where('metric_categories.name', 'like', '%' . $postData->search . '%');
            //                $query->orWhere('metric.name', 'like', '%' . $postData->search . '%');
            //                $query->orWhere('metric_groups.name', 'like', '%' . $postData->search . '%');
            //            });

            $query = $query->where(function ($query) use ($postData, $metricCategoryId) {
                $query->whereIn('id', $metricCategoryId);
                $query->orWhere('metric_categories.name', 'like', '%' . $postData->search . '%');
            });
        }
        $query = $query->orderBy('metric_categories.name')->skip($page - 1)->take($perPage)->get();

        return $query->toArray();
    }

    public function roles($postData)
    {
        $query = \DB::table('mst_roles')
            ->select('mst_roles.id', 'mst_roles.name')
            ->where('mst_roles.is_active', 1)
            ->whereNull('mst_roles.deleted_at');
        if ($postData->role_type == 1) {
            $query->where('mst_roles.role_type', $postData->role_type);
        }
        if ($postData->role_type == 2) {
            $query->where('mst_roles.role_type', $postData->role_type);
        }
        if ($postData->has('search') && !empty($postData->search)) {
            $query->where('mst_roles.name', 'like', '%' . $postData->search . '%');
        }
        return $query->orderBy('mst_roles.name')->get()->toArray();
    }

    public function user($postData, $page = 1, $perPage = 10)
    {
        $query = \DB::table('mst_users')
            ->select('mst_users.id', \DB::raw("CONCAT(mst_users.first_name,' ',mst_users.last_name) as name"), 'mst_users.email', 'mst_users.mobile_no')
            ->where('mst_users.is_active', 1)
            ->whereNull('mst_users.deleted_at');
        if (isset($postData['user_type']) == 1) {
            $query->where('mst_users.user_type', $postData['user_type']);
        }
        if (isset($postData['user_type']) == 2) {
            $query->where('mst_users.user_type', $postData['user_type']);
        }
        if (!empty($postData['search'])) {
            $query->where(function ($where) use ($postData) {
                $where->where(\DB::raw("CONCAT(mst_users.first_name,' ',mst_users.last_name)"), 'like', '%' . $postData['search'] . '%');
                $where->orWhere('mst_users.email', 'like', '%' . $postData['search'] . '%');
            });
        }
        $query       = $query->orderBy('mst_users.first_name');
        $count       = $query->count();
        $dataPerPage = $query->skip($page - 1)->take($perPage)->get()->toArray();

        return ['data' => $dataPerPage, 'count' => $count];
    }

    public function privilegesList($request)
    {
        $query = LovPrivileges::where('parent_id', 0)->with([
            'child' => function ($query) {
                $query->with(['child' => function ($query) {
                    $query->select(['id', 'group_id', 'parent_id', 'controller', 'name', 'is_active', 'menu_type']);
                }]);
            }
        ])->select('lov_privileges.id', 'lov_privileges.menu_type', 'lov_privileges.group_id', 'lov_privileges.parent_id', 'lov_privileges.controller', 'lov_privileges.name', 'lov_privileges.is_active');

        if ($request->has('menu_type') && !empty($request->menu_type)) {
            $query->where(function ($where) use ($request) {
                $where->where('lov_privileges.menu_type', $request->menu_type);
                $where->orWhere('lov_privileges.menu_type', 0);
            });
        }
        return $query->get()->toArray();
    }

    public function companyList($postData, $page = 1, $perPage = 100)
    {
        $query = \DB::table('mst_company')
            ->select('mst_company.id', 'mst_company.company_name', 'mst_company.user_id', 'mst_users.email', \DB::raw("CONCAT(mst_users.first_name,' ',mst_users.last_name) as name"))
            ->leftjoin('mst_users', 'mst_users.id', '=', 'mst_company.user_id')
            ->where('mst_company.is_active', 1)
            ->whereNull('mst_company.deleted_at');

        if ($postData->has('search') && !empty($postData->search)) {
            $query->where(function ($where) use ($postData) {
                $where->where('mst_company.company_name', 'like', '%' . $postData->search . '%');
                $where->orWhere('mst_users.email', 'like', '%' . $postData->search . '%');
                $where->orWhere(\DB::raw("CONCAT(mst_users.first_name,' ',mst_users.last_name)"), 'like', '%' . $postData->search . '%');
            });
        }
        return $query->orderBy('mst_company.company_name')->skip($page - 1)->take($perPage)->get()->toArray();
    }

    public function webUserList($request)
    {
        $query = \DB::table('mst_users')
            ->select('mst_users.id', \DB::raw("CONCAT(mst_users.first_name,' ',mst_users.last_name) as name"), 'mst_users.email', 'mst_users.mobile_no')
            ->leftjoin('mst_user_company_matrix as usercompany', 'usercompany.user_id', '=', 'mst_users.id')
            ->where('mst_users.user_type', 2)
            ->where('usercompany.company_id', $request->company_id)
            ->where('mst_users.is_active', 1)
            ->whereNull('mst_users.deleted_at')
            ->whereNull('usercompany.deleted_at');
        if (!empty($request->role_ids)) {
            $query->whereIn('usercompany.role_id', $request->role_ids);
        }
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($where) use ($request) {
                $where->where('mst_users.first_name', 'like', '%' . $request->search . '%');
                $where->orWhere('mst_users.last_name', 'like', '%' . $request->search . '%');
                $where->orWhere('mst_users.email', 'like', '%' . $request->search . '%');
            });
        }

        return $query->orderBy('mst_users.first_name')->get()->toArray();
    }

    public function stackModuleList()
    {
        $query = \DB::table('mst_stack_modules')
            ->select('mst_stack_modules.id', 'mst_stack_modules.key', 'mst_stack_modules.name', 'mst_stack_modules.can_copy', 'mst_stack_modules.is_active')
            ->where('mst_stack_modules.is_active', 1)
            ->whereNull('mst_stack_modules.deleted_at')->get();

        return $query;
    }

    public function metricAndMetricGroupList($request, $page = 1, $perPage = 100)
    {
        $query = MetricGroup::where('is_active', 1);
        if ((!empty($request->metric_category_id) && $request->metric_category_id != -1)) {
            $query = $query->where('metric_groups.metric_category_id', $request->metric_category_id);
        }
        if ((empty($request->metric_category_id))) {
            $query = $query->whereNull('metric_groups.metric_category_id');
        }
        if (!empty($request->search)) {
            $query->where(function ($where) use ($request) {
                $where->where('metric_groups.name', 'like', '%' . $request->search . '%');
            });
        }
        $query = $query->orderBy('metric_groups.name')->skip($page - 1)->take($perPage)->get();
        foreach ($query as $metricGroup) {
            $metrics = $metricGroup->metricBox()->select([
                'metric_group_matrix.id as matrix_id', 'metric.id as metric_id', 'metric.name as metric_name', 'metric.type',
                'metric.format_of_matrix', 'metric.expression', 'metric.expression_ids', 'metric.expression_readable', 'metric.expression_data',
                \DB::raw('(CASE
                    WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.DOLLAR.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.DOLLAR.name') . '"
                    WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.PERCENTAGE.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.PERCENTAGE.name') . '"
                    WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.QTY.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.QTY.name') . '"
                    ELSE "N/A"
                    END) AS display_format_of_matrix')
            ])
                ->leftjoin('metric', 'metric.id', '=', 'metric_group_matrix.metric_id');
            if ($request->company_id) {
                $metrics = $metrics->where(function ($where) use ($request) {
                    $where->where('metric.company_id', $request->company_id);
                    $where->orWhere('metric.is_admin', 1);
                });
            }
            $metrics = $metrics->get();

            $metricGroup->metricDetails = $metrics;
        }

        if (empty($query)) {
            return [];
        } else {
            return $query->toArray();
        }
    }
}
