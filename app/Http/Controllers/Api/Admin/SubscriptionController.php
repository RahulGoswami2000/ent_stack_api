<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Subscription\AddSubscriptionRequest;
use App\Http\Requests\Subscription\ChangeStatusRequest;
use App\Http\Requests\Subscription\StoreUpdateRequest;
use App\Services\Admin\SubscriptionService;
use Exception;
use Illuminate\Http\Request;

class SubscriptionController extends BaseController
{
    private $subscriptionService;

    public function __construct()
    {
        $this->subscriptionService = new subscriptionService;
    }

    /**
     * @OA\Get(
     * path="/admin/subscription",
     * tags = {"Subscription"},
     * summary = "To get the list of subscription, SUBSCRIPTION_INDEX",
     * operationId = "To get the list of subscription",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *       @OA\Parameter(
     *          name="search",
     *          description="search role name, description and price",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *          type="string"
     *      )
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

    //for listing the subscriptions

    public function index(Request $request)
    {
        try {
            $listData          = $this->subscriptionService->list($request);
            $count             = 0;
            $rows              = [];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['count']) && isset($listData['data'])) {
                list($count, $rows) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.subscription')]), compact('count', 'rows', 'downloadExportUrl'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.subscription')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/subscription/create",
     * tags = {"Subscription"},
     * summary = "To add subscription, SUBSCRIPTION_CREATE",
     * operationId = "To add subscription",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              @OA\Property(
     *                property="name",
     *                description = "Validation: min=3,max=50",
     *                type="string",
     *             ),
     *             @OA\Property(
     *                property="description",
     *                description = "Validation: min=3,max=200",
     *                type="string",
     *             ),
     *             @OA\Property(
     *                property="amount",
     *                description = "Validation: min=1",
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

    // for storing the subscriptions

    public function store(StoreUpdateRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->subscriptionService->store($request);
            \DB::commit();

            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.subscription')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.subscription')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/admin/subscription/{id}/details",
     * tags = {"Subscription"},
     * summary = "To check subscription details, SUBSCRIPTION_DETAILS, SUBSCRIPTION_UPDATE",
     * operationId = "To check subscription details",
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

    // to get the details of the particular subscription

    public function details($id)
    {
        try {
            $data = $this->subscriptionService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.subscription')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.subscription')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.subscription')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.subscription')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/subscription/{id}/update",
     * tags = {"Subscription"},
     * summary = "To update subscription details, SUBSCRIPTION_UPDATE",
     * operationId = "To update subscription details",
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
     *                property="name",
     *                description = "Validation: min=3,max=50",
     *                type="string",
     *             ),
     *             @OA\Property(
     *                property="description",
     *                description = "Validation: min=3,max=200",
     *                type="string",
     *             ),
     *             @OA\Property(
     *                property="amount",
     *                description = "Validation: min=1",
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

    // to update the subscription

    public function update($id, StoreUpdateRequest $request)
    {
        try {
            $data = $this->subscriptionService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.subscription')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.subscription')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->subscriptionService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.subscription')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.subscription')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Delete(
     * path="/admin/subscription/{id}/delete",
     * tags = {"Subscription"},
     * summary = "To delete subscription, SUBSCRIPTION_DELETE",
     * operationId = "To delete subscription",
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
            $data = $this->subscriptionService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.subscription')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.subscription')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->subscriptionService->destory($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.subscription')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.subscription')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/subscription/{id}/change-status",
     * tags = {"Subscription"},
     * summary = "To change status of subscription, SUBSCRIPTION_CHANGE_STATUS",
     * operationId = "To change status of subscription",
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

    // to change the status of the subscription

    public function changeStatus($id, ChangeStatusRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->subscriptionService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.subscription')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.subscription')])], 404, true);
            }

            $data = $this->subscriptionService->changeStatus($id, $request);

            if ($data->is_active == 1) {
                $is_active = 'activated';
            } else {
                $is_active = 'deactivated';
            }

            \DB::commit();
            return $this->sendResponse(__('messages.subscription_changed_successfully', ['moduleName' => $is_active]), true, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_status', ['moduleName' => __('labels.subscription')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
