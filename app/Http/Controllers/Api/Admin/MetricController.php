<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Metric\ChangeStatusRequest;
use App\Http\Requests\Metric\StoreUpdateRequest;
use App\Models\Metric;
use App\Services\Admin\MetricService;
use Exception;
use Illuminate\Http\Request;

class MetricController extends BaseController
{
    private $metricService;

    public function __construct()
    {
        $this->metricService = new MetricService();
    }

    /**
     * @OA\Post(
     * path="/admin/metrics",
     * tags = {"Metrics"},
     * summary = "To get the list of metrics, METRICS_INDEX",
     * operationId = "To get the list of metrics",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *    @OA\RequestBody(
     *       @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="export_type", type="string", description="csv, xlsx"),
     *               @OA\Property(property="filter_data", type="object",
     *                      @OA\Property(property="name", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                      @OA\Property(property="metric_category_name", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                      @OA\Property(property="id", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                      @OA\Property(property="is_active", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                      @OA\Property(property="created_at", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                      @OA\Property(property="updated_at", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *               ),
     *               @OA\Property(property="sort_data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="colId", type="string"),
     *                      @OA\Property(property="sort", type="string")
     *                  )
     *              ),
     *              @OA\Property(property="per_page", type="integer"),
     *              @OA\Property(property="page", type="integer"),
     *            )
     *        )
     *   ),
     *
     *      @OA\Response(
     *          response = 200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      ),
     * )
     */

    public function index(Request $request)
    {
        try {
            $postData          = $request->all();
            $pageNumber        = !empty($postData['page']) ? $postData['page'] : 1;
            $pageLimit         = !empty($postData['per_page']) ? $postData['per_page'] : 100;
            $skip              = ($pageNumber - 1) * $pageLimit;
            $listData          = $this->metricService->list($postData, $skip, $pageLimit, 1);
            $count             = 0;
            $rows              = [];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['data']) && isset($listData['count'])) {
                list($rows, $count) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.metric')]), compact('count', 'rows', 'downloadExportUrl'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metrics/create",
     * tags = {"Metrics"},
     * summary = "To add metrics, METRICS_CREATE",
     * operationId = "To add metrics",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *     @OA\Parameter(
     *       name="expression_ids[]",
     *       in="query",
     *       description="Group ids array",
     *       required= false,
     *       @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *           type="integer"
     *         )
     *       ),
     *   ),
     *      @OA\RequestBody(
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             required={"name","type","format_of_matrix"},
     *              @OA\Property(
     *                property="name",
     *                description = "Validation: min=3,max=50",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="type",
     *                description="1 = single and 2= calculation",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="format_of_matrix",
     *                description="1=$,2=% and 3=Qty",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="expression",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="metric_category_id",
     *                type="string",
     *             ),
     *             @OA\Property(
     *                property="expression_readable",
     *                type="string",
     *             ),
     *             @OA\Property(
     *                property="expression_data",
     *                description = "JSON format",
     *                type="string",
     *             ),
     *         ),
     *      ),
     *   ),
     *      @OA\Response(
     *          response = 200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      ),
     * )
     */

    public function store(StoreUpdateRequest $request)
    {
        try {
            \DB::beginTransaction();
            $request->merge(['is_admin' => 1]);
            $data = $this->metricService->store($request);
            \DB::commit();

            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.metric')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.metric')]), ['general' => $e->getMessage(), $e->getFile()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metrics/{id}/update",
     * tags = {"Metrics"},
     * summary = "To update metrics details, METRICS_UPDATE",
     * operationId = "To update metrics details",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *     ),
     *      @OA\Parameter(
     *       name="expression_ids[]",
     *       in="query",
     *       description="Group ids array",
     *       required= false,
     *       @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *           type="integer"
     *         )
     *       ),
     *   ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             required={"name","type","format_of_matrix"},
     *              @OA\Property(
     *                property="name",
     *                description = "Validation: min=3,max=50",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="type",
     *                description = "1=single and 2= calculation",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="format_of_matrix",
     *                description="1=$,2=% and 3=Qty",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="expression",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="metric_category_id",
     *                type="string",
     *             ),
     *         ),
     *      ),
     *   ),
     *      @OA\Response(
     *          response = 200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      ),
     * )
     */

    public function update($id, StoreUpdateRequest $request)
    {
        try {
            $data = $this->metricService->details($id);
            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.metric')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.metric')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->metricService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.metric')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.metric')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Delete(
     * path="/admin/metrics/{id}/delete",
     * tags = {"Metrics"},
     * summary = "To delete metrics, METRICS_DELETE",
     * operationId = "To delete metrics",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\Parameter(
     *          name = "id",
     *          in = "path",
     *          required = true,
     *          @OA\Schema(
     *              type ="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response = 200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      ),
     * )
     */

    public function destroy($id)
    {
        try {
            $data = $this->metricService->details($id);
            $metricCheck = $this->metricService->checkMetric($id);
            if (!empty($metricCheck)) {
                return $this->sendError(__('messages.already_in_use', ['moduleName' => __('labels.metric')]), ['general' => __('messages.already_in_use', ['moduleName' => __('labels.metric')])], 404, true);
            }

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.metric')])], 404, true);
            }

            if ($data->can_delete == 0) {
                return $this->sendError(__('messages.already_in_use', ['moduleName' => __('labels.metric')]), ['general' => __('messages.already_in_use', ['moduleName' => __('labels.metric')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->metricService->delete($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.metric')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.metric')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/admin/metrics/{id}/details",
     * tags = {"Metrics"},
     * summary = "To check Metrics details, METRICS_UPDATE, METRICS_DETAILS",
     * operationId = "To check Metrics details",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\Parameter(
     *          name = "id",
     *          in = "path",
     *          required = true,
     *          @OA\Schema(
     *              type ="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response = 200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      ),
     * )
     */

    public function details($id)
    {
        try {
            $data = $this->metricService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.metric')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.metric')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.metric')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.metric')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metrics/{id}/change-status",
     * tags = {"Metrics"},
     * summary = "To change status of Metrics, METRICS_CHANGE_STATUS",
     * operationId = "To change status of Metrics",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\Parameter(
     *          name = "id",
     *          in = "path",
     *          required = false,
     *          @OA\Schema(
     *              type ="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name = "is_active",
     *          in = "query",
     *          required = true,
     *          @OA\Schema(
     *              type ="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response = 200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      ),
     * )
     */

    public function changeStatus($id, ChangeStatusRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->metricService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.metric')])], 404, true);
            }

            $data = $this->metricService->changeStatus($id, $request);
            if ($data->is_active == 1) {
                $is_active = 'activated';
            } else {
                $is_active = 'deactivated';
            }
            \DB::commit();
            return $this->sendResponse(__('messages.metric_module_changed_successfully', ['module' => __('labels.metric'), 'moduleName' => $is_active]), true, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_status', ['moduleName' => __('labels.metric')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
