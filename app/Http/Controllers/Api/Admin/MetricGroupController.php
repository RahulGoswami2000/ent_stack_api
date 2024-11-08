<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\MetricGroup\AddRemoveMetricRequest;
use App\Http\Requests\MetricGroup\ChangeStatusRequest;
use App\Http\Requests\MetricGroup\StoreUpdateRequest;
use App\Models\MetricGroup;
use App\Services\Admin\MetricGroupService;
use Exception;
use Illuminate\Http\Request;

class MetricGroupController extends BaseController
{
    private $metricGroupService;

    public function __construct()
    {
        $this->metricGroupService = new MetricGroupService;
    }

    /**
     * @OA\Post(
     * path="/admin/metric-group",
     * tags = {"Metric Group"},
     * summary = "To get the list of metricgroup, METRIC_GROUP_INDEX",
     * operationId = "To get the list of metricgroup",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
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
     *                      
     *                      @OA\Property(property="id", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                       @OA\Property(property="is_active", type="object",
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
     *     ),
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
            $listData          = $this->metricGroupService->list($postData, $skip, $pageLimit);
            $count             = 0;
            $rows              = [];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['data']) && isset($listData['count'])) {
                list($rows, $count) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.metric_box')]), compact('count', 'rows', 'downloadExportUrl'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric_box')]), ['general' => $e->getMessage(), $e->getFile()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metric-group/create",
     * tags = {"Metric Group"},
     * summary = "To add metricgroup, METRIC_GROUP_CREATE",
     * operationId = "To add metricgroup",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *      @OA\Parameter(
     *       name="metric_id[]",
     *       in="query",
     *       description="Metric ids array",
     *       required=false,
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
     *             required={"name"},
     *              @OA\Property(
     *                property="name",
     *                description = "Validation: min=3,max=50",
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

    public function store(StoreUpdateRequest $request)
    {
        try {

            \DB::beginTransaction();
            $data = $this->metricGroupService->store($request);
            \DB::commit();

            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.metric_box')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.metric_box')]), ['general' => $e->getMessage(), $e->getFile()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metric-group/{id}/update",
     * tags = {"Metric Group"},
     * summary = "To update metricgroup details, METRIC_GROUP_UPDATE",
     * operationId = "To update metricgroup details",
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
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             required={"name"},
     *              @OA\Property(
     *                property="name",
     *                description = "Validation: min=3,max=50",
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
            $data = $this->metricGroupService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.metric_box')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.metric_box')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->metricGroupService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.metric_box')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.metric_box')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Delete(
     * path="/admin/metric-group/{id}/delete",
     * tags = {"Metric Group"},
     * summary = "To delete metricgroup, METRIC_GROUP_DELETE",
     * operationId = "To delete metricgroup",
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
            $data = $this->metricGroupService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric_box')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.metric_box')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->metricGroupService->delete($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.metric_box')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.metric_box')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/admin/metric-group/{id}/details",
     * tags = {"Metric Group"},
     * summary = "To check Metric Group details, METRIC_GROUP_UPDATE, METRIC_GROUP_DETAILS",
     * operationId = "To check Metric Group details",
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
            $data = $this->metricGroupService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.metric_box')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.metric_box')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.metric_box')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.metric_box')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metric-group/{id}/change-status",
     * tags = {"Metric Group"},
     * summary = "To change status of Metric Group, METRIC_GROUP_CHANGE_STATUS",
     * operationId = "To change status of Metric Group",
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
            $data = $this->metricGroupService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric_box')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.metric_box')])], 404, true);
            }

            $data = $this->metricGroupService->changeStatus($id, $request);
            if ($data->is_active == 1) {
                $is_active = 'activated';
            } else {
                $is_active = 'deactivated';
            }
            \DB::commit();
            return $this->sendResponse(__('messages.metric_module_changed_successfully', ['module' => __('labels.metric_box'), 'moduleName' => $is_active]), true, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_status', ['moduleName' => __('labels.metric_box')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metric-group/change-category",
     * tags = {"Metric Group"},
     * summary = "To change category of Metric Group, METRIC_GROUP_UPDATE",
     * operationId = "To change category of Metric Group",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\Parameter(
     *          name = "id",
     *          in = "query",
     *          required = true,
     *          @OA\Schema(
     *              type ="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name = "metric_category_id",
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

    public function changeCategory(Request $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->metricGroupService->changeCategory($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_changed_successfully', ['moduleName' => __('labels.category')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_module_name', ['moduleName' => __('labels.category')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metric-group/add-metric",
     * tags = {"Metric Group"},
     * summary = "To add metric in Metric Group, METRIC_GROUP_UPDATE",
     * operationId = "To add metric in Metric Group",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *      @OA\Parameter(
     *       name="metric_id[]",
     *       in="query",
     *       description="Metric ids array",
     *       required=true,
     *       @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *           type="integer"
     *         )
     *       ),
     *   ),
     *      @OA\Parameter(
     *          name = "metric_group_id",
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

    public function addMetric(AddRemoveMetricRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->metricGroupService->addMetric($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.metric')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.metric')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metric-group/remove-metric",
     * tags = {"Metric Group"},
     * summary = "To remove metric from Metric Group, METRIC_GROUP_UPDATE",
     * operationId = "To remove metric from Metric Group",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\Parameter(
     *          name = "metric_group_id",
     *          in = "query",
     *          required = true,
     *          @OA\Schema(
     *              type ="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name = "metric_id",
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

    public function removeMetric(AddRemoveMetricRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->metricGroupService->removeMetric($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.metric')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.metric')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metric-group/{id}/metric-list",
     * tags = {"Metric Group"},
     * summary = "To get the metric list of matric box",
     * operationId = "To get the metric list of matric box",
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
     *          @OA\RequestBody(
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
     *                      
     *                      @OA\Property(property="id", type="object",
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
     *     ),
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

    public function metricList($id, Request $request)
    {
        try {
            $postData          = $request->all();
            $pageNumber        = !empty($postData['page']) ? $postData['page'] : 1;
            $pageLimit         = !empty($postData['per_page']) ? $postData['per_page'] : 100;
            $skip              = ($pageNumber - 1) * $pageLimit;
            $listData          = $this->metricGroupService->metricList($id, $postData);
            $count             = 0;
            $rows              = [];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['data']) && isset($listData['count'])) {
                list($rows, $count) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.metric_box')]), compact('count', 'rows', 'downloadExportUrl'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric_box')]), ['general' => $e->getMessage(), $e->getFile()], 500, true);
        }
    }
}
