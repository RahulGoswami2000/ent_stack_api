<?php

namespace App\Repositories;

use App\Models\MetricGroup;
use App\Models\MetricGroupMatrix;
use App\Traits\CommonTrait;

class MetricGroupRepository extends BaseRepository
{
    use CommonTrait;

    private $metricGroup, $metricGroupMatrix;

    public function __construct()
    {
        $this->metricGroup       = new MetricGroup();
        $this->metricGroupMatrix = new MetricGroupMatrix();
    }

    public function list($postData, $page = 1, $perPage = 10)
    {
        $query = \DB::table('metric_groups')
            ->select(
                'metric_groups.id',
                'metric_groups.name',
                'metric_groups.is_active',
                'metric_groups.metric_category_id',
                \DB::raw('IF(metric_categories.name IS NULL, "N/A", metric_categories.name) AS metric_category_name'),
                \DB::raw('IF(metric_groups.is_active = 1,"' . __('labels.active') . '","' . __('labels.inactive') . '") AS display_status')
            )
            ->leftjoin('metric_categories', 'metric_categories.id', '=', 'metric_groups.metric_category_id')
            ->whereNull('metric_groups.deleted_at');

        if (!empty($postData['filter_data'])) {
            foreach ($postData['filter_data'] as $key => $value) {
                if (in_array($key, ["name", "metric_category_name"])) {
                    switch ($key) {
                        case "metric_category_name":
                            $key = \DB::raw('IF(metric_categories.name IS NULL, "N/A", metric_categories.name)');
                            break;
                        default:
                            $key = 'metric_groups.' . $key;
                            break;
                    }
                    $query = $this->createWhere('text', $key, $value, $query);
                }
                if (in_array($key, ["id", "metric_category_id"])) {
                    $key   = 'metric_groups.' . $key;
                    $query = $this->createWhere('number', $key, $value, $query);
                }
                if (in_array($key, ["is_active"])) {
                    $key   = 'metric_groups.' . $key;
                    $query = $this->createWhere('set', $key, $value, $query);
                }
                if (in_array($key, ["updated_at", "created_at"])) {
                    $key   = 'metric_groups.' . $key;
                    $query = $this->createWhere('date', $key, $value, $query);
                }
            }
        }

        $orderBy   = 'metric_groups.updated_at';
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

    public function store($request)
    {
        $data = MetricGroup::create([
            'name'               => $request->name,
            'metric_category_id' => $request->metric_category_id,

        ]);
        $arr  = $request->metric_id;
        foreach ($arr as $key => $value) {
            MetricGroupMatrix::create([
                'metric_group_id' => $data->id,
                'metric_id'       => $request->metric_id[$key],
            ]);
        }
        return $data;
    }

    public function update($id, $request)
    {
        $data = $this->metricGroup->find($id);
        $data->update([
            'name' => $request->name,
        ]);
        return $data;
    }

    public function delete($id)
    {
        return $this->metricGroup->find($id)->delete();
    }

    public function details($id)
    {
        $dataDetails = $this->metricGroup->find($id);

        if (empty($dataDetails)) {
            return null;
        }
        return $dataDetails;
    }

    public function changeStatus($id, $request)
    {
        $data = $this->metricGroup->find($id);
        $data->update([
            'is_active' => $request->is_active,
        ]);
        return $data;
    }

    public function changeCategory($request)
    {
        return MetricGroup::where('id', $request->id)
            ->update(['metric_category_id' => $request->metric_category_id]);
    }

    public function addMetric($request)
    {
        $arr = $request->metric_id;
        foreach ($arr as $key => $value) {
            $checkCount = MetricGroupMatrix::where([
                'metric_group_id' => $request->metric_group_id,
                'metric_id'       => $request->metric_id[$key],
            ])->count();
            if (empty($checkCount)) {
                $data = MetricGroupMatrix::create([
                    'metric_group_id' => $request->metric_group_id,
                    'metric_id'       => $request->metric_id[$key],
                ]);
            }
        }
        return true;
    }

    public function removeMetric($request)
    {
        return MetricGroupMatrix::where('metric_group_id', $request->metric_group_id)->where('metric_id', $request->metric_id)->delete();
    }

    public function metricList($id, $postData)
    {
        $query = \DB::table('metric_group_matrix')
            ->select(
                'metric_group_matrix.id as metric_group_matrix_id',
                'metric_group_matrix.metric_id',
                'metric_group_matrix.metric_group_id',
                'metric.id as metric_id',
                'metric.name',
                'metric.metric_category_id',
                'metric.format_of_matrix',
                \DB::raw('(CASE
                    WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.DOLLAR.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.DOLLAR.name') . '"
                    WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.PERCENTAGE.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.PERCENTAGE.name') . '"
                    WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.QTY.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.QTY.name') . '"
                    ELSE "N/A"
                    END) AS display_format_of_matrix'),
                \DB::raw('IF(metric_categories.name IS NULL, "N/A", metric_categories.name) AS metric_category_name'),
            )
            ->leftjoin('metric', 'metric.id', '=', 'metric_group_matrix.metric_id')
            ->leftjoin('metric_categories', 'metric_categories.id', '=', 'metric.metric_category_id')
            ->where('metric_group_matrix.metric_group_id', $id)
            ->whereNull('metric_group_matrix.deleted_at');

        if (!empty($postData['filter_data'])) {
            foreach ($postData['filter_data'] as $key => $value) {
                if (in_array($key, ["name", "metric_category_name"])) {
                    switch ($key) {
                        case "metric_category_name":
                            $key = \DB::raw('IF(metric_categories.name IS NULL, "N/A", metric_categories.name)');
                            break;
                        default:
                            $key = 'metric.' . $key;
                            break;
                    }
                    $query = $this->createWhere('text', $key, $value, $query);
                }
                if (in_array($key, ["id"])) {
                    $key   = 'metric.' . $key;
                    $query = $this->createWhere('number', $key, $value, $query);
                }
            }
        }

        $orderBy   = 'metric_group_matrix.updated_at';
        $orderType = (isset($postData['order_by']) && $postData['order_by'] == 1) ? 'asc' : 'desc';
        if (!empty($postData['sort_data'])) {
            $orderBy   = $postData['sort_data'][0]['col_id'];
            $orderType = $postData['sort_data'][0]['sort'];
        }

        $query       = $query->orderBy($orderBy, $orderType);
        $count       = $query->count();
        $dataPerPage = $query->get()->toArray();

        return ['data' => $dataPerPage, 'count' => $count];
    }
}
