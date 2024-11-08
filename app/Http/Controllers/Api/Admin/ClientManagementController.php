<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientManagement\ChangeStatusRequest;
use App\Http\Requests\ClientManagement\StoreUpdateRequest;
use App\Services\Admin\ClientManagementService;
use Exception;
//use Exception;
use Illuminate\Http\Request;

class ClientManagementController extends BaseController
{
    private $clientManagementService;

    public function __construct()
    {
        $this->clientManagementService = new ClientManagementService;
    }

    /**
     * @OA\Post(
     * path="/admin/client-management",
     * tags = {"Client Management"},
     * summary = "To get the list of client, CLIENT_MANAGEMENT_INDEX",
     * operationId = "To get the list of client",
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
     *                      @OA\Property(property="email", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                      @OA\Property(property="mobile_no", type="object",
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
     *                      @OA\Property(property="company_name", type="object",
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
            $listData          = $this->clientManagementService->list($pageData, $skip, $pageLimit);
            $count             = 0;
            $rows              = [];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['data']) && isset($listData['count'])) {
                list($rows, $count) = array_values($listData);
            }
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.client')]), compact('count', 'rows', 'downloadExportUrl'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.client')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/admin/client-management/{id}/details",
     * tags = {"Client Management"},
     * summary = "To check client details, CLIENT_MANAGEMENT_DETAILS, CLIENT_MANAGEMENT_UPDATE",
     * operationId = "To check client details",
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
            $data = $this->clientManagementService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.client')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.client')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.client')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.client')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/client-management/{id}/update",
     * tags = {"Client Management"},
     * summary = "To update client details, CLIENT_MANAGEMENT_UPDATE    ",
     * operationId = "To update client details",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="only those id could be entered who are also in company",
     *      @OA\Schema(
     *           type="integer"
     *      )
     *     ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             required={"first_name","last_name","email","mobile_no"},
     *              @OA\Property(
     *                property="first_name",
     *                description = "Validation: min=3,max=50",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="last_name",
     *                description = "Validation: min=3,max=50",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="email",
     *                type="string",
     *                description="Validations: min=3, max=70",
     *             ),
     *              @OA\Property(
     *                property="mobile_no",
     *                type="string",
     *                description="Validations: min=4, max=20 regex:/^[0-9]+$/",
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
            $data = $this->clientManagementService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.client')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.client')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->clientManagementService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.client')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.client')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/client-management/{id}/change-status",
     * tags = {"Client Management"},
     * summary = "To change status of Client, CLIENT_MANAGEMENT_CHANGE_STATUS",
     * operationId = "To change status of Client",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\Parameter(
     *          name = "id",
     *          in = "path",
     *          required = true,
     *          description="select company is to be activate or deactivated",
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
            $data = $this->clientManagementService->changeStatus($id, $request);
            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.client')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.client')])], 404, true);
            }
            if ($data->is_active == 1) {
                $is_accepted = 'activated';
            } else {
                $is_accepted = 'deactivated';
            }
            \DB::commit();
            return $this->sendResponse(__('messages.client_changed_successfully', ['moduleName' => $is_accepted]), true, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_status', ['moduleName' => __('labels.client')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/client-management/{id}/users",
     * tags = {"Client Management"},
     * summary = "To get the list of client users",
     * operationId = "To get the list of client users",
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
     *      @OA\RequestBody(
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
     *                      @OA\Property(property="email", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                      @OA\Property(property="is_active", type="object",
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

    public function users($id, Request $request)
    {
        try {
            $pageData          = $request->all();
            $pageNumber        = !empty($pageData['page']) ? $pageData['page'] : 1;
            $pageLimit         = !empty($pageData['per_page']) ? $pageData['per_page'] : 100;
            $skip              = ($pageNumber - 1) * $pageLimit;
            $listData          = $this->clientManagementService->users($id, $pageData, $skip, $pageLimit);
            $count             = 0;
            $rows              = [];
            $companyDetails    = (object)[];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['count']) && isset($listData['data']) && isset($listData['companyDetails'])) {
                list($rows, $count, $companyDetails) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.team_member')]), compact('count', 'rows', 'downloadExportUrl', 'companyDetails'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.team_member')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/client-management/{id}/user-change-status",
     * tags = {"Client Management"},
     * summary = "To change status of Client user list, CLIENT_MANAGEMENT_USER_CHANGE_STATUS",
     * operationId = "To change status of Client user list",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\Parameter(
     *          name = "id",
     *          in = "path",
     *          required = true,
     *          description="select user is to be activate or deactivated",
     *          @OA\Schema(
     *              type ="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name = "company_id",
     *          in = "query",
     *          required = true,
     *          description="select company is to be activate or deactivated",
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

    public function userChangeStatus($id, ChangeStatusRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->clientManagementService->userChangeStatus($id, $request);
            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.team_member')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.team_member')])], 404, true);
            }
            if ($data->is_active == 1) {
                $is_accepted = 'activated';
            } else {
                $is_accepted = 'deactivated';
            }
            \DB::commit();
            return $this->sendResponse(__('messages.team_member_changed_successfully', ['moduleName' => $is_accepted]), true, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_status', ['moduleName' => __('labels.team_member')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
