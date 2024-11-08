<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\MetricCategory\ChangeStatusRequest;
use App\Http\Requests\MetricCategory\StoreUpdateRequest;
use App\Models\Metric;
use App\Models\MetricCategory;
use App\Services\Admin\MetricCategoryService;
use App\Services\Admin\MetricService;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;

class MetricCategoryController extends BaseController
{
    private $metricCategoryService, $metricService;

    public function __construct()
    {
        $this->metricCategoryService = new MetricCategoryService;
        $this->metricService         = new MetricService;
    }


    /**
     * @OA\Post(
     * path="/admin/metric-categories",
     * tags = {"MetricCategories"},
     * summary = "To get the list of category, METRIC_CATEGORIES_INDEX",
     * operationId = "To get the list of category",
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
            $pageData          = $request->all();
            $pageNumber        = !empty($pageData['page']) ? $pageData['page'] : 1;
            $pageLimit         = !empty($pageData['per_page']) ? $pageData['per_page'] : 100;
            $skip              = ($pageNumber - 1) * $pageLimit;
            $listData          = $this->metricCategoryService->list($pageData, $skip, $pageLimit);
            $count             = 0;
            $rows              = [];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['data']) && isset($listData['count'])) {
                list($rows, $count) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.category')]), compact('count', 'rows', 'downloadExportUrl'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.category')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metric-categories/create",
     * tags = {"MetricCategories"},
     * summary = "To add category, METRIC_CATEGORIES_CREATE",
     * operationId = "To add category",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *    @OA\Parameter(
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
            $data = $this->metricCategoryService->store($request);
            \DB::commit();

            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.category')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.category')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metric-categories/{id}/update",
     * tags = {"MetricCategories"},
     * summary = "To update category details, METRIC_CATEGORIES_UPDATE",
     * operationId = "To update category details",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="for updating the selected Category",
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
            $data = $this->metricCategoryService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.category')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.category')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->metricCategoryService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.category')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.category')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Delete(
     * path="/admin/metric-categories/{id}/delete",
     * tags = {"MetricCategories"},
     * summary = "To delete category, METRIC_CATEGORIES_DELETE",
     * operationId = "To delete category",
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

    // to  delete the subscription

    public function destroy($id)
    {
        try {
            $data        = $this->metricCategoryService->details($id);
            $metricCheck = $this->metricCategoryService->checkMetricExists($id);
            if (!empty($metricCheck)) {
                return $this->sendError(__('messages.already_in_use', ['moduleName' => __('labels.metric_category')]), ['general' => __('messages.already_in_use', ['moduleName' => __('labels.metric_category')])], 404, true);
            }

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric_category')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.metric_category')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->metricCategoryService->delete($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.metric_category')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.metric_category')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metric-categories/{id}/remove-category",
     * tags = {"MetricCategories"},
     * summary = "To remove metric, METRIC_CATEGORIES_UPDATE",
     * operationId = "To remove metric",
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

    public function removeCategory($id, Request $request)
    {
        try {
            $data = $this->metricCategoryService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.metric')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->metricCategoryService->removeCategory($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.category')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.metric')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metric-categories/{id}/add-metric",
     * tags = {"MetricCategories"},
     * summary = "To add metric, METRIC_CATEGORIES_UPDATE",
     * operationId = "To add metric",
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

    public function addMetric($id, Request $request)
    {
        try {
            $data = $this->metricCategoryService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric')]), ['general' => __('messages.failed_to_save_module_name', ['moduleName' => __('labels.metric')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->metricCategoryService->addMetric($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.metric')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.metric')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/admin/metric-categories/{id}/details",
     * tags = {"MetricCategories"},
     * summary = "To check MetricCategories details, METRIC_CATEGORIES_UPDATE, METRIC_CATEGORIES_DETAILS",
     * operationId = "To check MetricCategories details",
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
            $data = $this->metricCategoryService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.category')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.category')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.category')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.category')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/metric-categories/{id}/change-status",
     * tags = {"MetricCategories"},
     * summary = "To change status of MetricCategories, METRIC_CATEGORIES_CHANGE_STATUS",
     * operationId = "To change status of MetricCategories",
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
            $data = $this->metricCategoryService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.category')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.category')])], 404, true);
            }

            $data = $this->metricCategoryService->changeStatus($id, $request);
            if ($data->is_active == 1) {
                $is_active = 'activated';
            } else {
                $is_active = 'deactivated';
            }
            \DB::commit();
            return $this->sendResponse(__('messages.metric_module_changed_successfully', ['module' => __('labels.metric_category'),'moduleName' => $is_active]), true   ,true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_status', ['moduleName' => __('labels.category')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
