<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Roles\ChangeStatusRequest;
use App\Http\Requests\Roles\StoreUpdateRequest;
use App\Services\Admin\RolesService;
use Exception;

class RolesController extends BaseController
{

    private $rolesService;

    public function __construct()
    {
        $this->rolesService = new RolesService;
    }

    /**
     * @OA\Get(
     * path="/admin/roles",
     * tags = {"Roles Managment"},
     * summary = "To get the list of roles ,ROLES_INDEX",
     * operationId = "To get the list of roles",
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
            $listData = $this->rolesService->list();
            $count    = 0;
            $rows     = [];
            if (!empty($listData) && isset($listData['count']) && isset($listData['data'])) {
                list($rows, $count) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.roles')]), compact('count', 'rows'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.roles')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/roles/create",
     * tags = {"Roles Managment"},
     * summary = "To add Roles,ROLES_CREATE",
     * operationId = "To add Roles",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             required={"roles_name"},
     *              @OA\Property(
     *                property="roles_name",
     *                type="string",
     *                description="Validations: min=3, max=50",
     *             ),
     *              @OA\Property(
     *                property="role_type",
     *                type="integer",
     *                description="Role Type 1,2",
     *             ),
     *             @OA\Property(
     *                property="privileges",
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
            $data = $this->rolesService->store($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.roles')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.roles')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     ** path="/admin/roles/{id}/details",
     *   tags={"Roles Managment"},
     *   summary="to get roles details,ROLES_DETAILS",
     *   operationId="roles-details",
     *  security={{"bearer_token":{}}, {"x_localization":{}}},
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *   @OA\Response(
     *      response=403,
     *      description="Forbidden"
     *   ),
     *   @OA\Response(
     *      response=500,
     *      description="Server Error"
     *   )
     *)
     **/
    public function details($id)
    {
        try {
            $data = $this->rolesService->details($id);
            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.roles')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.roles')])], 404, true);
            }
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.roles')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.roles')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/roles/{id}/update",
     * tags = {"Roles Managment"},
     * summary = "To update roles details,ROLES_UPDATE",
     * operationId = "To update roles details",
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
     *       @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              @OA\Property(
     *                property="roles_name",
     *                type="string",
     *                description="Validations: min=3, max=50",
     *             ),
     *            @OA\Property(
     *                property="role_type",
     *                type="integer",
     *                description="Role Type 1,2",
     *             ),
     *             @OA\Property(
     *                property="privileges",
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
            $data = $this->rolesService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.roles')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.roles')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->rolesService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.roles')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.roles')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Delete(
     * path="/admin/roles/{id}/delete",
     * tags = {"Roles Managment"},
     * summary = "To delete roles,ROLES_DELETE",
     * operationId = "To delete roles",
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
            $data = $this->rolesService->details($id);
            if (!empty(in_array($id, \DB::table('mst_roles')->whereNull('deleted_at')->whereIn('name', config('global.ROLES'))->get()->pluck('id')->toArray()))) {
                return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.role')]), ['general' => __('messages.failed_to_delete_module_name', ['moduleName' => __('labels.role')])], 400, true);
            }

            if (!empty(\DB::table('mst_users')->whereNull('deleted_at')->where('role_id', $id)->count())) {
                return $this->sendError(__('messages.already_in_use', ['moduleName' => __('labels.role')]), ['general' => __('messages.already_in_use', ['moduleName' => __('labels.role')])], 400, true);
            }

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.roles')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.roles')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->rolesService->destory($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.role')]), $data, true);

        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.roles')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/roles/{id}/change-status",
     * tags = {"Roles Managment"},
     * summary = "To change status of roles ,ROLES_CHANGE_STATUS",
     * operationId = "To change status of roles",
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
            $data = $this->rolesService->details($id);
            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.roles')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.roles')])], 404, true);
            }

            if (!empty(in_array($id, \DB::table('mst_roles')->whereNull('deleted_at')->whereIn('name', config('global.ROLES'))->get()->pluck('id')->toArray()))) {
                return $this->sendError(__('messages.failed_to_change_status', ['moduleName' => __('labels.role')]), ['general' => __('messages.failed_to_change_status', ['moduleName' => __('labels.role')])], 404, true);
            }

            if (!empty(\DB::table('mst_users')->whereNull('deleted_at')->where('role_id', $id)->count())) {
                return $this->sendError(__('messages.already_in_use_change_status', ['moduleName' => __('labels.role')]), ['general' => __('messages.already_in_use_change_status', ['moduleName' => __('labels.role')])], 400, true);
            }

            $data = $this->rolesService->changeStatus($id, $request);
            if ($data->is_active == 1) {
                $is_active = 'activated';
            } else {
                $is_active = 'deactivated';
            }
            \DB::commit();
            return $this->sendResponse(__('messages.role_changed_successfully', ['moduleName' => $is_active]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_status', ['moduleName' => __('labels.roles')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
