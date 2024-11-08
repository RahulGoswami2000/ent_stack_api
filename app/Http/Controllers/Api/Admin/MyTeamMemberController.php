<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\TeamMember\ChangeStatusRequest;
use App\Http\Requests\TeamMember\StoreUpdateRequest;
use App\Http\Requests\TeamMember\ClientAssignRequest;
use App\Services\Admin\MyTeamMemberService;
use Exception;
use Illuminate\Http\Request;

class MyTeamMemberController extends BaseController
{
    private $teamMemberService;

    public function __construct()
    {
        $this->teamMemberService = new MyTeamMemberService;
    }

    /**
     * @OA\Post(
     * path="/admin/team-member",
     * tags = {"My Team Member"},
     * summary = "To get the list of team member,MY_TEAM_MEMBER_INDEX",
     * operationId = "To get the list team member",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="export_type", type="string", description="csv, xlsx"),
     *               @OA\Property(property="filter_data", type="object",
     *                      @OA\Property(property="first_name", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *               @OA\Property(property="last_name", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                @OA\Property(property="email", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                @OA\Property(property="mobile_no", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                @OA\Property(property="date_of_birth", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="date_from", type="string"),
     *                               @OA\Property(property="date_to", type="string"),
     *                      ),
     *                @OA\Property(property="start_date", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="date_from", type="string"),
     *                               @OA\Property(property="date_to", type="string"),
     *                      ),
     *                @OA\Property(property="created_by_name", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                 @OA\Property(property="updated_by_name", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                 @OA\Property(property="created_at", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="date_from", type="string"),
     *                               @OA\Property(property="date_to", type="string"),
     *                      ),
     *                 @OA\Property(property="updated_at", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="date_from", type="string"),
     *                               @OA\Property(property="date_to", type="string"),
     *                      ),
     *                 @OA\Property(property="is_active", type="object",
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

            $listData = $this->teamMemberService->list($postData, $skip, $pageLimit);
            $count    = 0;
            $rows     = [];
            if (!empty($listData) && isset($listData['count']) && isset($listData['data'])) {
                list($rows, $count) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.team_member')]), compact('count', 'rows'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.team_member')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     *    path="/admin/team-member/create",
     *    tags={"My Team Member"},
     *    summary = "To create of team member, MY_TEAM_MEMBER_CREATE",
     *    operationId = "To get the create team member",
     *    security={{"bearer_token":{}}, {"x_localization":{}}},
     *   @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              required={"first_name","last_name","country_code","mobile_no","password","email","role_id",},
     *             @OA\Property(
     *                      property="profile_image",
     *                      type="file",
     *                      description="Validations: filetype=jpg,jpeg,png, max=1MB",
     *                  ),
     *              @OA\Property(
     *                property="first_name",
     *                type="string",
     *                description="Validations: min=3, max=50",
     *             ),
     *             @OA\Property(
     *                property="last_name",
     *                type="string",
     *                description="Validations: min=3, max=50",
     *             ),
     *             @OA\Property(
     *                property="mobile_no",
     *                type="string",
     *                description="Validations: min=4, max=20 regex:/^[0-9]+$/",
     *             ),
     *          @OA\Property(
     *                property="role_id",
     *                type="integer",
     *             ),
     *          @OA\Property(
     *                property="job_role",
     *                type="string",
     *                description="Validations: min=2, max=50",
     *             ),
     *           @OA\Property(
     *                property="email",
     *                type="string",
     *                description="Validations: min=3, max=70",
     *             ),
     *           @OA\Property(
     *                property="password",
     *                type="string",
     *                description="Validations: min=8, max=20",
     *             ),
     *         ),
     *      ),
     *   ),
     *  @OA\Response(
     *        response=200,
     *        description="User Create Successfully",
     *        @OA\MediaType(
     *            mediaType="multipart/form-data",
     *        )
     *    ),
     *    @OA\Response(
     *        response=401,
     *        description="Unauthorized"
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Invalid request"
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="not found"
     *    ),
     * )
     */

    public function store(StoreUpdateRequest $request)
    {
        try {

            // \Log::info(\DB::getQueryLog());
            \DB::beginTransaction();
            $data = $this->teamMemberService->store($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.team_member')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.team_member')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     ** path="/admin/team-member/{id}/details",
     *   tags={"My Team Member"},
     *   summary="Team Member Details, MY_TEAM_MEMBER_DETAILS",
     *   operationId="team-member-details",
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
            $data = $this->teamMemberService->details($id);
            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.team_member')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.team_member')])], 404, true);
            }
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.team_member')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.team_member')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/team-member/{id}/update",
     * tags = {"My Team Member"},
     * summary = "To update team members, MY_TEAM_MEMBER_UPDATE",
     * operationId = "To update team members",
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
     *              required={"profile_image","first_name","last_name","country_code","mobile_no","role_id","job_role","email",},
     *             @OA\Property(
     *                 property="profile_image",
     *                 type="file",
     *                 description="Validations: filetype=jpg,jpeg,png, max=1MB",
     *              ),
     *              @OA\Property(
     *                property="first_name",
     *                type="string",
     *                description="Validations: min=3, max=50",
     *             ),
     *             @OA\Property(
     *                property="last_name",
     *                type="string",
     *                description="Validations: min=3, max=50",
     *             ),
     *             @OA\Property(
     *                property="mobile_no",
     *                type="string",
     *                description="Validations: min=4, max=20",
     *             ),
     *          @OA\Property(
     *                property="role_id",
     *                type="integer",
     *             ),
     *          @OA\Property(
     *                property="job_role",
     *                type="string",
     *                description="Validations: min=2, max=50",
     *             ),
     *           @OA\Property(
     *                property="email",
     *                type="string",
     *                description="Validations: min=3, max=70",
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
            $data = $this->teamMemberService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.team_member')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.team_member')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->teamMemberService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.team_member')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.team_member')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    public function destroy($id)
    {
        try {
            $data = $this->teamMemberService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.team_member')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.team_member')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->teamMemberService->destory($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.team_member')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.team_member')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/team-member/{id}/change-status",
     * tags = {"My Team Member"},
     * summary = "To change status of team member, MY_TEAM_MEMBER_CHANGE_STATUS",
     * operationId = "To change status of team member",
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
     *          description="Validations: 0,1",
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
            $data = $this->teamMemberService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.team_member')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.team_member')])], 404, true);
            }

            $data = $this->teamMemberService->changeStatus($id, $request);
            if ($data->is_active == 1) {
                $is_active = 'activated';
            } else {
                $is_active = 'deactivated';
            }
            \DB::commit();
            return $this->sendResponse(__('messages.email_module_changed_successfully', ['module' => __('labels.team_member'), 'moduleName' => $is_active]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_status', ['moduleName' => __('labels.team_member')]), ['general' => $e->getMessage()], 500, true);
        }
    }
    /**
     * @OA\Post(
     * path="/admin/team-member/client-assign",
     * tags = {"My Team Member"},
     * summary = "To add client assign, MY_TEAM_MEMBER_ASSIGN_CLIENT",
     * operationId = "To add client assign",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *    @OA\Parameter(
     *       name="company_id[]",
     *       in="query",
     *       description="Company ids array",
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
     *             required={"user_id"},
     *              @OA\Property(
     *                property="user_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="client_assign",
     *                description = "Client Assign Type 0 OR 1",
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
    public function clientAssign(ClientAssignRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->teamMemberService->clientAssign($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.client_assign')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.client_assign')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
