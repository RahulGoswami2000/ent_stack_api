<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\CompanyProject\BulkUpdateRequest;
use App\Http\Requests\CompanyProject\StoreUpdateRequest;
use App\Services\Web\CompanyProjectService;
use Exception;
use Illuminate\Http\Request;

class CompanyProjectController extends BaseController
{

    private $CompanyProjectService;

    public function __construct()
    {
        $this->CompanyProjectService = new CompanyProjectService;
    }
    /**
     * @OA\Get(
     * path="/company-project",
     * tags = {"Company Project"},
     * summary = "To get the list of company project",
     * operationId = "To get the list of company project",
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
            $listData          = $this->CompanyProjectService->list();
            $count             = 0;
            $rows              = [];
            if (!empty($listData) && isset($listData['count']) && isset($listData['data'])) {
                list($rows, $count) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.company_project')]), compact('count', 'rows'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.company_project')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/company-project/create",
     * tags = {"Company Project"},
     * summary = "To add Company Project",
     * operationId = "To add company project",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *
     *              @OA\Property(
     *                property="company_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="name",
     *                description = "Validation: min=3,max=100",
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
            $data = $this->CompanyProjectService->store($request);
            \DB::commit();

            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.company_project')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.company_project')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/company-project/{id}/details",
     * tags = {"Company Project"},
     * summary = "To check company project details",
     * operationId = "To check company project details",
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
            $data = $this->CompanyProjectService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.company_project')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.company_project')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.company_project')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.company_project')]), ['general' => $e->getMessage()], 500, true);
        }
    }
    /**
     * @OA\Post(
     * path="/company-project/{id}/update",
     * tags = {"Company Project"},
     * summary = "To update company project details ",
     * operationId = "To update company project details",
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
     *              @OA\Property(
     *                property="company_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="name",
     *                description = "Validation: min=3,max=100",
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
            $data = $this->CompanyProjectService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.company_project')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.company_project')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->CompanyProjectService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.company_project')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.company_project')]), ['general' => $e->getMessage()], 500, true);
        }
    }
    /**
     * @OA\Delete(
     * path="/company-project/{id}/delete",
     * tags = {"Company Project"},
     * summary = "To delete company project",
     * operationId = "To delete company project",
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
            $data = $this->CompanyProjectService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.company_project')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.company_project')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->CompanyProjectService->destory($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.company_project')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.company_project')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/company-project/bulk-update",
     * tags = {"Company Project"},
     * summary = "To bulk update Company Project",
     * operationId = "To bulk update Company Project",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *       @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="project_id", type="integer"),
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

    public function bulkUpdate(BulkUpdateRequest $request) {
        try {
            \DB::beginTransaction();
            $data = $this->CompanyProjectService->bulkUpdate($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.company_project')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.company_project')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
