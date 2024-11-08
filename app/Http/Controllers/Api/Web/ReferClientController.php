<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\ReferClient\ChangeStatusRequest;
use App\Http\Requests\ReferClient\StoreUpdateRequest;
use App\Services\Web\ReferClientService;
use Exception;

class ReferClientController extends BaseController
{

    private ReferClientService $referClientService;

    public function __construct()
    {
        $this->referClientService = new ReferClientService;
    }
    /**
     * @OA\Get(
     * path="/refer-client",
     * tags = {"Refer Client"},
     * summary = "To get the list of refer client",
     * operationId = "To get the list of refer client",
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
            $userId = \Auth::user()->id;
            $listData          = $this->referClientService->list($userId);
            $count             = 0;
            $rows              = [];
            if (!empty($listData) && isset($listData['count']) && isset($listData['data'])) {
                list($rows, $count) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.refer_client')]), compact('count', 'rows'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.refer_client')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/refer-client/create",
     * tags = {"Refer Client"},
     * summary = "To add Refer Client",
     * operationId = "To add refer client",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
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
     *             @OA\Property(
     *                property="email",
     *                type="string",
     *                 description="Validations: min=3, max=70",
     *             ),
     *             @OA\Property(
     *                property="referal_code",
     *                type="string",
     *                 description="Validations: min=1, max=70",
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
            $data = $this->referClientService->store($request);
            \DB::commit();

            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.refer_client')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.refer_client')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/refer-client/{id}/details",
     * tags = {"Refer Client"},
     * summary = "To check refer_client details",
     * operationId = "To check refer_client details",
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
            $data = $this->referClientService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.refer_client')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.refer_client')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.refer_client')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.refer_client')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/refer-client/{id}/update",
     * tags = {"Refer Client"},
     * summary = "To update refer client details",
     * operationId = "To update refer client details",
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
     *             @OA\Property(
     *                property="email",
     *                type="string",
     *                 description="Validations: min=3, max=70",
     *             ),
     *             @OA\Property(
     *                property="referal_code",
     *                type="string",
     *                 description="Validations: min=1, max=70",
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
            $data = $this->referClientService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.refer_client')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.refer_client')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->referClientService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.refer_client')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.refer_client')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Delete(
     * path="/refer-client/{id}/delete",
     * tags = {"Refer Client"},
     * summary = "To delete refer_client",
     * operationId = "To delete refer_client",
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
            $data = $this->referClientService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.referral')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.refer_client')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->referClientService->destory($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.referral')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.referral')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/refer-client/{id}/change-status",
     * tags = {"Refer Client"},
     * summary = "To change status of Refer Client",
     * operationId = "To change status of Refer Client",
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
             $data = $this->referClientService->details($id);
 
             if (empty($data)) {
                 return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.referral')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.referral')])], 404, true);
             }
 
             $data = $this->referClientService->changeStatus($id, $request);
             if ($data->is_active == 1) {
                 $is_active = 'activated';
             } else {
                 $is_active = 'deactivated';
             }
             \DB::commit();
             return $this->sendResponse(__('messages.metric_module_changed_successfully', ['module' => __('labels.referral'), 'moduleName' => $is_active]), true, true);
         } catch (Exception $e) {
             \DB::rollBack();
             return $this->sendError(__('messages.failed_to_change_status', ['moduleName' => __('labels.referral')]), ['general' => $e->getMessage()], 500, true);
         }
     }
}
