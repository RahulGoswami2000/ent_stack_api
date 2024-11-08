<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Referral\ChangeStatusRequest;
use App\Http\Requests\Referral\ReferralRequest;
use App\Services\Admin\ReferralsService;
use Exception;
use Illuminate\Http\Request;

class ReferralsController extends BaseController
{
    private ReferralsService $referralsService;

    public function __construct()
    {
        $this->referralsService = new ReferralsService;
    }

    /**
     * @OA\Post(
     * path="/admin/referrals",
     * tags = {"Referrals"},
     * summary = "To get the list of referrals, REFERRALS_INDEX",
     * operationId = "To get the list referrals",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="export_type", type="string", description="csv, xlsx"),
     *               @OA\Property(property="filter_data", type="object",
     *                      @OA\Property(property="company_name", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *               @OA\Property(property="referring_user", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                @OA\Property(property="email", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                @OA\Property(property="is_active", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                @OA\Property(property="created_at", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                 ),
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
            $postData   = $request->all();
            $pageNumber = !empty($postData['page']) ? $postData['page'] : 1;
            $pageLimit  = !empty($postData['per_page']) ? $postData['per_page'] : 100;
            $skip       = ($pageNumber - 1) * $pageLimit;

            $listData = $this->referralsService->list($postData, $skip, $pageLimit);
            $count    = 0;
            $rows     = [];
            if (!empty($listData) && isset($listData['count']) && isset($listData['data'])) {
                list($rows, $count) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.referral')]), compact('count', 'rows'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.referral')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/referrals/accept-referral",
     * tags = {"Referrals"},
     * summary = "To accept the referral",
     * operationId = "To accept the referral",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              required={"id","is_referred"},
     *              @OA\Property(
     *                property="id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="is_referred",
     *                type="integer",
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

    public function referralAcccept(ReferralRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->referralsService->referralAccept($request);
            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.referral')]), ['general' => __('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.referral')])], 404, true);
            }

            \DB::commit();
            return $this->sendResponse(__('messages.invitation_accepted', ['moduleName' => __('labels.referral')]), $data, true);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.referral')]), ['general' => $e->getMessage()], 500, true);
        }
    }
    /**
     * @OA\Delete(
     * path="/admin/referrals/{id}/delete",
     * tags = {"Referrals"},
     * summary = "To delete the referral, REFERRALS_DELETE",
     * operationId = "To delete referral",
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
            $data = $this->referralsService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.c')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.referral')])], 404, true);
            }

            if (!empty($data->status)) {
                return $this->sendError(__('messages.this_client_referrals_already_accepted'), ['general' => __('messages.this_client_referrals_already_accepted')], 400, true);
            }
            \DB::beginTransaction();
            $data = $this->referralsService->destory($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.referral')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.referral')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/referrals/{id}/change-status",
     * tags = {"Referrals"},
     * summary = "To change status of Referral",
     * operationId = "To change status of Referral",
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
            $data = $this->referralsService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.referral')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.referral')])], 404, true);
            }

            $data = $this->referralsService->changeStatus($id, $request);
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
