<?php

namespace App\Repositories;

use App\Models\Metric;
use App\Models\MetricCategory;
use App\Models\MetricGroup;
use App\Traits\CommonTrait;

class MetricCategoryRepository extends BaseRepository
{
    use CommonTrait;

    private $metricCategory;
    private $metric;

    public function __construct()
    {
        $this->metricCategory = new MetricCategory();
        $this->metric         = new Metric();
    }

    public function list($listData, $page = 1, $perPage = 10)
    {
        $query = \DB::table('metric_categories')
            ->select('metric_categories.id', 'metric_categories.name', 'metric_categories.is_active', \DB::raw('IF(metric_categories.is_active = 1,"' .  __('labels.active')  . '","' .  __('labels.inactive') . '") AS display_status'))
            ->whereNull('metric_categories.deleted_at');
        if (!empty($listData['filter_data'])) {
            foreach ($listData['filter_data'] as $key => $value) {
                if (in_array($key, ["name"])) {
                    $key   = 'metric_categories.' . $key;
                    $query = $this->createWhere('text', $key, $value, $query);
                }
                if (in_array($key, ["id"])) {
                    $key   = 'metric_categories.' . $key;
                    $query = $this->createWhere('number', $key, $value, $query);
                }
                if (in_array($key, ["is_active"])) {
                    $key   = 'metric_categories.' . $key;
                    $query = $this->createWhere('set', $key, $value, $query);
                }
                if (in_array($key, ["updated_at", "created_at"])) {
                    $key   = 'metric_categories.' . $key;
                    $query = $this->createWhere('date', $key, $value, $query);
                }
            }
        }

        $orderBy   = 'metric_categories.updated_at';
        $orderType = (isset($listData['order_by']) && $listData['order_by'] == 1) ? 'asc' : 'desc';
        if (!empty($listData['sort_data'])) {
            $orderBy   = $listData['sort_data'][0]['col_id'];
            $orderType = $listData['sort_data'][0]['sort'];
        }

        $query       = $query->orderBy($orderBy, $orderType);
        $count       = $query->count();
        $dataPerPage = $query->skip($page)->take($perPage)->get()->toArray();
        return ['data' => $dataPerPage, 'count' => $count];
    }

    public function store($request)
    {
        $data = MetricCategory::create([
            'name' => $request->name,
        ]);

        if (!empty($request->metric_id)) {
            $this->metric->whereIn('id', $request->metric_id)->whereNull('metric_category_id')
                ->update(['metric_category_id' => $data->id]);
        }

        return $data;
    }

    public function update($id, $request)
    {
        $data = $this->metricCategory->find($id);
        $data->update([
            'name' => $request->name,
        ]);
        return $data;
    }

    public function destroy($id)
    {
        return $this->metricCategory->find($id)->delete();
    }

    public function removeCategory($id, $request)
    {
        return $this->metric->whereIn('id', $request->metric_id)
            ->update(['metric_category_id' => null]);
    }

    public function addMetric($id, $request)
    {
        return $this->metric->whereIn('id', $request->metric_id)->whereNull('metric_category_id')
            ->update(['metric_category_id' => $id]);
    }

    public function details($id)
    {
        $dataDetails = $this->metricCategory->find($id);

        if (empty($dataDetails)) {
            return null;
        }

        $dataDetails->metrics = $dataDetails->metrics()->where('is_active', 1)->select('id', 'name')->get();


        return $dataDetails;
    }

    public function changeStatus($id, $request)
    {
        $data = $this->metricCategory->find($id);
        $data->update([
            'is_active' => $request->is_active,
        ]);
        return $data;
    }

    public function checkMetricExists($id)
    {
        $metric = Metric::where('metric_category_id', $id)->count();
        $metricGroup = MetricGroup::where('metric_category_id', $id)->count();

        return ($metric || $metricGroup);
    }
}
