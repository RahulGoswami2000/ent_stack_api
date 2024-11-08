<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\WebTeamMember\StoreUpdateRequest;
use App\Http\Requests\WebTeamMember\UpdateRoleRequest;
use App\Services\Web\TeamMemberService;
use Exception;
use Illuminate\Http\Request;

class TeamMemberController extends BaseController
{
    private $teamMemberService;

    public function __construct()
    {
        $this->teamMemberService = new TeamMemberService;
    }

    /**
     * @OA\Get(
     * path="/team-member",
     * tags = {"Team Member"},
     * summary = "To get the list of team member",
     * operationId = "To get the list of team member",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\Parameter(
     *          name = "company_id",
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

    public function index(Request $request)
    {
        try {
            $listData          = $this->teamMemberService->list($request);
            $count             = 0;
            $rows              = [];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['count']) && isset($listData['data'])) {
                list($rows, $count) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.team_member')]), compact('count', 'rows', 'downloadExportUrl'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.team_member')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/team-member/create",
     * tags = {"Team Member"},
     * summary = "To add team member",
     * operationId = "To add team member",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *       @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="email", type="string"),
     *               @OA\Property(property="role_id", type="integer"),
     *               @OA\Property(property="job_role", type="string"),
     *               @OA\Property(property="company_id", type="integer"),
     *               @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="project_id", type="integer"),
     *                      @OA\Property(property="company_stack_modules_id", type="integer"),
     *                      @OA\Property(property="company_stack_category_id", type="integer"),
     *                      @OA\Property(property="stack_table_id", type="integer"),
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

    public function store(StoreUpdateRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->teamMemberService->create($request);
            \DB::commit();
            return $this->sendResponse(__('messages.team_member_invited_successfully'), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_invite_member'), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/team-member/{id}/details",
     * tags = {"Team Member"},
     * summary = "To check team member details",
     * operationId = "To check team member details",
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
     * path="/team-member/{id}/update",
     * tags = {"Team Member"},
     * summary = "To update team member details",
     * operationId = "To update team member details",
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
     *             mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="role_id", type="integer"),
     *               @OA\Property(property="job_role", type="string"),
     *               @OA\Property(property="company_id", type="integer"),
     *               @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="project_id", type="integer"),
     *                      @OA\Property(property="company_stack_modules_id", type="integer"),
     *                      @OA\Property(property="company_stack_category_id", type="integer"),
     *                      @OA\Property(property="stack_table_id", type="integer"),
     *                  )
     *              ),
     *         )
     *      )
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
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.team_member')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.team_member')])], 404, true);
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

    /**
     * @OA\Post(
     * path="/team-member/{id}/update-role",
     * tags = {"Team Member"},
     * summary = "To update role details",
     * operationId = "To update role details in team member",
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
     *                property="role_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="user_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="company_id",
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
    public function updateRole($id, UpdateRoleRequest $request)
    {
        try {
            $data = $this->teamMemberService->companyDetails($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.role')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.role')])], 404, true);
            }

            $ownerRoleId = \DB::table('mst_roles')->whereNull('deleted_at')->where('name', config('global.ROLES.OWNER'))->first()->id;
            if ($request->role_id == $ownerRoleId) {
                return $this->sendError(__('messages.cannot_update_owner_role', ['moduleName' => __('labels.role')]), ['general' => __('messages.cannot_update_owner_role', ['moduleName' => __('labels.role')])], 404, true);
            }

            if (!empty(\DB::table('mst_user_company_matrix')->whereNull('deleted_at')->where('role_id', $ownerRoleId)->where('user_id', $request->user_id)->where('company_id', $request->company_id)->count())) {
                return $this->sendError(__('messages.cannot_update_owner_role', ['moduleName' => __('labels.role')]), ['general' => __('messages.cannot_update_owner_role', ['moduleName' => __('labels.role')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->teamMemberService->updateRole($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.role')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.role')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/team-member/{id}/accept-invitation",
     * tags = {"Team Member"},
     * summary = "To accept the invitation",
     * operationId = "To accept the invitation",
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
     *                property="is_accepted",
     *                type="integer",
     *             ),
     *
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

    public function acceptInvitation($id, Request $request)
    {
        try {
            $data = $this->teamMemberService->companyMatrixDetails($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.invitation')]), ['general' => __('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.invitation')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->teamMemberService->acceptInvitation($id, $request);
            if ($data->is_accepted == 2) {
                $is_accepted = 'Rejected';
            } else {
                $is_accepted = 'Accepted';
            }

            \DB::commit();
            return $this->sendResponse(__('messages.invitation_accepted', ['moduleName' => $is_accepted]), $data, true);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.invitation')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
