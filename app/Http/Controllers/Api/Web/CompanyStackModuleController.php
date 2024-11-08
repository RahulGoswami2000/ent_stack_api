<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompantStackModule\DuplicateStackRequest;
use App\Http\Requests\CompanyStackModule\BulkUpdateRequest;
use App\Http\Requests\CompanyStackModule\StoreUpdateRequest;
use App\Http\Requests\CompanyStackModule\ChangeStatusRequest;
use App\Http\Requests\ProjectCategory\DuplicateCategoryRequest;
use App\Services\Web\CompanyStackModuleService;
use Exception;
use Illuminate\Http\Request;

class CompanyStackModuleController extends BaseController
{
    private $companyStackModuleService;

    public function __construct()
    {
        $this->companyStackModuleService = new CompanyStackModuleService;
    }
    /**
     * @OA\Post(
     * path="/company-stack-module/create",
     * tags = {"Company Stack Module"},
     * summary = "To add company stack",
     * operationId = "To add company stack",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(
     *                property="company_id",
     *                description = "enter valid company id",
     *                type="integer",
     *             ),
     *             @OA\Property(
     *                property="project_id",
     *                description = "enter valid project id",
     *                type="integer",
     *             ),
     *             @OA\Property(
     *                property="name",
     *                description="Validations: min=3, max=100",
     *                type="string",
     *             ),
     *             @OA\Property(
     *                property="stack_modules_id",
     *                description = "enter stack module id",
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
            $data = $this->companyStackModuleService->store($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }
    /**
     * @OA\Get(
     * path="/company-stack-module/{id}/details",
     * tags = {"Company Stack Module"},
     * summary = "To check company stack module details",
     * operationId = "To check company stack module details",
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
            $data = $this->companyStackModuleService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.stack')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.stack')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/company-stack-module/{id}/update",
     * tags = {"Company Stack Module"},
     * summary = "To update company stack module details",
     * operationId = "To update company stack module details",
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
     *
     *             @OA\Property(
     *                property="company_id",
     *                description = "enter valid company id",
     *                type="integer",
     *             ),
     *             @OA\Property(
     *                property="project_id",
     *                description = "enter valid project id",
     *                type="integer",
     *             ),
     *             @OA\Property(
     *                property="name",
     *                 description="Validations: min=3, max=100",
     *                type="string",
     *             ),
     *             @OA\Property(
     *                property="stack_modules_id",
     *                description = "enter stack module id",
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
            $data = $this->companyStackModuleService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.stack')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->companyStackModuleService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }
    /**
     * @OA\Delete(
     * path="/company-stack-module/{id}/delete",
     * tags = {"Company Stack Module"},
     * summary = "To delete company stack module",
     * operationId = "To delete company stack module",
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
            $data = $this->companyStackModuleService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.stack')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->companyStackModuleService->destory($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }
    /**
     * @OA\Post(
     * path="/company-stack-module/{id}/change-status",
     * tags = {"Company Stack Module"},
     * summary = "To change status of company stack module",
     * operationId = "To change company stack module",
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
     *          description="Validations:0,1",
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
            $data = $this->companyStackModuleService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.stack')])], 404, true);
            }

            $data = $this->companyStackModuleService->changeStatus($id, $request);

            if ($data->is_active == 1) {
                $is_active = 'activated';
            } else {
                $is_active = 'deactivated';
            }

            \DB::commit();
            return $this->sendResponse(__('messages.company_stack_module_changed_successfully', ['moduleName' => $is_active]), true, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_status', ['moduleName' => __('labels.stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/company-stack-module/bulk-update",
     * tags = {"Company Stack Module"},
     * summary = "To bulk update Company Stack",
     * operationId = "To bulk update Company Stack",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *       @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="company_stack_modules_id", type="integer"),
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
            $data = $this->companyStackModuleService->bulkUpdate($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.stacks')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.stacks')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/company-stack-module/duplicate-stack",
     * tags = {"Company Stack Module"},
     * summary = "To duplicate Company Stack Module",
     * operationId = "To duplicate Company Stack Module",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              required = {"company_id","project_id","name","company_stack_modules_id"},
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

    public function duplicateStack(DuplicateStackRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->companyStackModuleService->duplicateStack($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_duplicated_successfully', ['moduleName' => __('labels.stacks')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_duplicate_module_name', ['moduleName' => __('labels.stacks')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
