<?php

namespace App\Repositories;

use App\Models\Metric;
use App\Models\MetricGroupMatrix;
use App\Traits\CommonTrait;

class MetricRepository extends BaseRepository
{
    use CommonTrait;

    private $metric;

    public function __construct()
    {
        $this->metric = new Metric();
    }

    public function list($postData, $page = 1, $perPage = 10, $accessType)
    {
        $query = \DB::table('metric')
            ->select(
                'metric.id',
                'metric.name',
                'metric.is_active',
                'metric.can_delete',
                'metric.type',
                'metric.metric_category_id',
                'metric.format_of_matrix',
                \DB::raw('(CASE
                    WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.DOLLAR.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.DOLLAR.name') . '"
                    WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.PERCENTAGE.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.PERCENTAGE.name') . '"
                    WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.QTY.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.QTY.name') . '"
                    ELSE "N/A"
                    END) AS display_format_of_matrix'),
                'metric.expression',
                'metric.expression_ids',
                'metric.is_active',
                \DB::raw('IF(metric_categories.name IS NULL, "N/A", metric_categories.name) AS metric_category_name'),
                \DB::raw('IF(metric.is_active = 1,"' . __('labels.active') . '","' . __('labels.inactive') . '") AS display_status'),
                'metric.expression_readable',
                'metric.expression_data'
            )
            ->leftjoin('metric_categories', 'metric_categories.id', '=', 'metric.metric_category_id')
            ->whereNull('metric.deleted_at')
            ->where('metric.is_admin', $accessType);
        if (!empty($postData['filter_data'])) {
            foreach ($postData['filter_data'] as $key => $value) {
                if (in_array($key, ["name", "display_format_of_matrix", "metric_category_name"])) {
                    switch ($key) {
                        case "display_format_of_matrix":
                            $key = \DB::raw('(CASE
                        WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.DOLLAR.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.DOLLAR.name') . '"
                        WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.PERCENTAGE.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.PERCENTAGE.name') . '"
                        WHEN metric.format_of_matrix = ' . config('global.FORMAT_OF_MATRIX.QTY.id') . ' THEN "' . config('global.FORMAT_OF_MATRIX.QTY.name') . '"
                        ELSE "N/A"
                        END)');
                            break;
                        case "metric_category_name":
                            $key = \DB::raw('IF(metric_categories.name IS NULL, "N/A", metric_categories.name)');
                            break;
                        default:
                            $key = 'metric.' . $key;
                            break;
                    }
                    $query = $this->createWhere('text', $key, $value, $query);
                }

                if (in_array($key, ["id", "metric_category_id", "format_of_matrix"])) {
                    $key   = 'metric.' . $key;
                    $query = $this->createWhere('number', $key, $value, $query);
                }
                if (in_array($key, ["is_active"])) {
                    $key   = 'metric.' . $key;
                    $query = $this->createWhere('set', $key, $value, $query);
                }
                if (in_array($key, ["metric.updated_at", "metric.created_at"])) {
                    $key   = 'metric.' . $key;
                    $query = $this->createWhere('date', $key, $value, $query);
                }
            }
        }

        $orderBy   = 'metric.updated_at';
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
        $data = Metric::create([
            'name'                => $request->name,
            'type'                => $request->type,
            'metric_category_id'  => $request->metric_category_id,
            'format_of_matrix'    => $request->format_of_matrix,
            'expression'          => $request->expression,
            'expression_ids'      => !empty($request->expression_ids) ? json_encode($request->expression_ids) : null,
            'expression_readable' => $request->expression_readable,
            'expression_data'     => !empty($request->expression_data) ? json_encode($request->expression_data) : null,
            'is_admin'            => $request->is_admin,
            'company_id'          => $request->company_id,
        ]);

        if (!empty($request->expression_ids)) {
            $id = Metric::whereIn('id', $request->expression_ids)->get();

            foreach ($id as $dataDetails) {
                $dataDetails->update([
                    'can_delete' => 0,
                ]);
            }
        }

        return $data;
    }

    public function update($id, $request)
    {
        $data = $this->metric->find($id);

        $expressionIds = json_decode($data->expression_ids);
        if (!empty($data->expression_ids) && !empty($expressionIds)) {
            Metric::whereIn('id', $expressionIds)->update([
                'can_delete' => 1,
            ]);
        }

        $data->update([
            'name'                => $request->name,
            'type'                => $request->type,
            'metric_category_id'  => $request->metric_category_id,
            'format_of_matrix'    => $request->format_of_matrix,
            'expression'          => $request->expression,
            'expression_ids'      => !empty($request->expression_ids) ? json_encode($request->expression_ids) : null,
            'expression_readable' => $request->expression_readable,
            'expression_data'     => !empty($request->expression_data) ? json_encode($request->expression_data) : null,
        ]);

        !empty($request->expression_ids) && Metric::whereIn('id', $request->expression_ids)->update([
            'can_delete' => 0,
        ]);

        return $data;
    }

    public function delete($id)
    {
        return $this->metric->find($id)->delete();
    }


    public function traverse_arr(&$array, $searchValue, $updateValue)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->traverse_arr($array[$key], $searchValue, $updateValue);
            } else if ($key === 'value' && $array['value'] == $searchValue) {
                $array['text'] = $updateValue;
            }
        }
    }

    public function details($id)
    {
        $dataDetails = $this->metric->find($id);
        if (empty($dataDetails)) {
            return null;
        }
        $expressionIds         = json_decode($dataDetails->expression_ids);
        if (!empty($expressionIds)) {
            $metricData            = Metric::whereIn('id', $expressionIds)->get();
            $newExpression         = $dataDetails->expression;
            $newExpressionReadable = $dataDetails->expression_readable;
            $newExpressionData     = json_decode($dataDetails->expression_data, true);
            $matches               = array();
            $regex                 = "/\|\|\|([a-zA-Z0-9_:]*)\|\|\|/";
            preg_match_all($regex, $dataDetails->expression, $matches);
            foreach ($metricData as $metric) {
                $name      = $metric->name;
                $metric_id = $metric->id;
                for ($i = 0; $i < sizeof($matches[0]); $i++) {
                    $expressionDataValue = explode(':', $matches[1][$i]);
                    if ($expressionDataValue[0] == $metric_id) {
                        $newExpression         = str_replace($matches[0][$i], '|||' . $metric_id . ':' . $name . '|||', $newExpression);
                        $newExpressionReadable = str_replace(" " . $expressionDataValue[1] . " ", ' ' . $name . ' ', $newExpressionReadable);

                        $this->traverse_arr($newExpressionData, $metric_id, $name);
                    }
                }
            }

            $dataDetails
                ->update([
                    'expression'          => $newExpression,
                    'expression_data'     => json_encode($newExpressionData),
                    'expression_readable' => $newExpressionReadable,
                ]);
        }
        $dataDetails->category = $dataDetails->category()->first(['id', 'name']);
        return $dataDetails;
    }

    public function changeStatus($id, $request)
    {
        $data = $this->metric->find($id);
        $data->update([
            'is_active' => $request->is_active,
        ]);

        return $data;
    }

    public function checkMetric($id)
    {
        return MetricGroupMatrix::where('metric_id', $id)->count();
    }
}
