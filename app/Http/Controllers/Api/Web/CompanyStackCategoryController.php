<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\ProjectCategory\BulkUpdateRequest;
use App\Http\Requests\ProjectCategory\DuplicateCategoryRequest;
use App\Http\Requests\ProjectCategory\StoreUpdateRequest;
use App\Services\Web\CompanyStackCategoryService;
use Exception;
use Illuminate\Http\Request;

class CompanyStackCategoryController extends BaseController
{
    private $projectCategoryService;

    public function __construct()
    {
        $this->projectCategoryService = new CompanyStackCategoryService;
    }

    /**
     * @OA\Get(
     * path="/companystack-category",
     * tags = {"Company Stack Category"},
     * summary = "To get the list of Company Stack Category",
     * operationId = "To get the list of Company Stack Category",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
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

    public function index()
    {
        try {
            $listData = $this->projectCategoryService->list();
            $count = 0;
            $row = [];
            if (!empty($listData) && isset($listData['count']) && isset($listData['data'])) {
                list($count, $row) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.subcategory')]), compact('count', 'row'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.subcategory')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/companystack-category/create",
     * tags = {"Company Stack Category"},
     * summary = "To add Company Stack Category",
     * operationId = "To add Company Stack Category",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              required = {"company_id","project_id","name"},
     *              @OA\Property(
     *                property="company_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="project_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="name",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="company_stack_modules_id",
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
            $data = $this->projectCategoryService->store($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.subcategory')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.subcategory')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/companystack-category/{id}/details",
     * tags = {"Company Stack Category"},
     * summary = "To get Company Stack Category details",
     * operationId = "To get Company Stack Category details",
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
            $data = $this->projectCategoryService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.subcategory')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.subcategory')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.subcategory')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.subcategory')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/companystack-category/{id}/update",
     * tags = {"Company Stack Category"},
     * summary = "To update Company Stack Category details",
     * operationId = "To update Company Stack Category details",
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
     *              required = {"name"},
     *              @OA\Property(
     *                property="name",
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
            $data = $this->projectCategoryService->details($id);
            // dd($data);
            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.subcategory')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.subcategory')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->projectCategoryService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.subcategory')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.subcategory')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Delete(
     * path="/companystack-category/{id}/delete",
     * tags = {"Company Stack Category"},
     * summary = "To delete Company Stack Category",
     * operationId = "To delete Company Stack Category",
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
            $data = $this->projectCategoryService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.subcategory')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.subcategory')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->projectCategoryService->delete($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.subcategory')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.subcategory')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/companystack-category/update-sequence",
     * tags = {"Company Stack Category"},
     * summary = "To change the sequence",
     * operationId = "To change the sequence",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *    *    @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(property="project", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="sequence", type="integer")
     *                  ),
     *              ),
     *           ),
     *       ),
     *    ),
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

    public function updateSequence(Request $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->projectCategoryService->updateSequence($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.sequence')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.sequence')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/companystack-category/duplicate-category",
     * tags = {"Company Stack Category"},
     * summary = "To duplicate Company Stack Category",
     * operationId = "To duplicate Company Stack Category",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              required = {"company_id","project_id","name","company_stack_modules_id","company_stack_category_id"},
     *              @OA\Property(
     *                property="company_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="project_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="name",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="company_stack_modules_id",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="company_stack_category_id",
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

    public function duplicateCategory(DuplicateCategoryRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->projectCategoryService->duplicateCategory($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_duplicated_successfully', ['moduleName' => __('labels.subcategory')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_duplicate_module_name', ['moduleName' => __('labels.subcategory')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/companystack-category/bulk-update",
     * tags = {"Company Stack Category"},
     * summary = "To bulk update Category",
     * operationId = "To bulk update Category",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *       @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="category_id", type="integer"),
     *                      @OA\Property(property="is_deleted", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="sequence", type="integer"),
     *                  )
     *              ),
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

    public function bulkUpdate(BulkUpdateRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->projectCategoryService->bulkUpdate($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.subcategory')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.subcategory')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
