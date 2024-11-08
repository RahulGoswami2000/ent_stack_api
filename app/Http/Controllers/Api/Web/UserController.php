<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Auth\User\UserRequest;
use App\Http\Requests\Auth\User\ChangeStatusRequestUser;
use App\Http\Requests\Auth\User\ProfileImageRequest;
use App\Http\Requests\TeamStack\AssignStackRequest;
use App\Services\Web\UserService;
use Exception;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    /**
     * @OA\Get(
     * path="/user/",
     * tags = {"Web User"},
     * summary = "To get the list of user",
     * operationId = "To get the list of user",
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
            $data = $this->userService->list();
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.user')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.user')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     ** path="/user/{id}/details",
     *   tags={"Web User"},
     *   summary="User Details",
     *   operationId="user-details",
     *   security={{"bearer_token":{}}, {"x_localization":{}}},
     *
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
            $user = $this->userService->details($id);
            if (empty($user)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.user')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.user')])], 404, true);
            }
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.user')]), $user, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.user')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     ** path="/user/{id}/update",
     *   tags={"Web User"},
     *   summary="Update user",
     *   operationId="user-update",
     *   security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *               required={"first_name","last_name","mobile_no","date_of_birth","start_date","password","email","role_id",},
     *                  @OA\Property(
     *                      property="first_name",
     *                      type="string",
     *                      description="Validations: min=3, max=50",
     *                  ),
     *                  @OA\Property(
     *                      property="last_name",
     *                      type="string",
     *                      description="Validations: min=3, max=50",
     *                  ),
     *                 @OA\Property(
     *                      property="mobile_no",
     *                      type="string",
     *                      description="Validations: min=4, max=20 regex:/^[0-9]+$/",
     *                  ),
     *                 @OA\Property(
     *                      property="country_code",
     *                      type="string",
     *                      description="Validations: min=2",
     *                  ),
     *                 @OA\Property(
     *                      property="date_of_birth",
     *                      type="string",
     *                      description="1999-07-07 Use This Date Format",
     *                  ),
     *                 @OA\Property(
     *                      property="start_date",
     *                      type="string",
     *                      description="1999-07-07 Use This Date Format",
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      description="Validations: min=8, max=20",
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      description="Validations: min=3, max=70",
     *                  ),
     *                  @OA\Property(
     *                      property="role_id",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="organization",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="profile_image",
     *                      type="file",
     *                      description="Validations: filetype=jpg,jpeg,png, max=1MB",
     *                  ),
     *              )
     *          )
     *      ),
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
    public function update($id, UserRequest $request)
    {
        try {
            $postData = $request->all();
            $user     = $this->userService->details($id);
            if (empty($user)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.user')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.user')])], 404, true);
            }
            \DB::beginTransaction();
            $user = $this->userService->update($id, $postData);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.user')]), compact('user'), true);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.user')]), ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     ** path="/user/{id}/change-status",
     *   tags={"Web User"},
     *   summary="Change User Status",
     *   operationId="user-change",
     *   security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="is_active",
     *      in="query",
     *      required=true,
     *      description="Validations:0,1",
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
    public function changeStatus($id, ChangeStatusRequestUser $request)
    {
        try {
            \DB::beginTransaction();
            $user = $this->userService->details($id);
            if (empty($user)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.user')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.user')])], 404, true);
            }
            $user = $this->userService->changeStatus($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.user')]), $user, false);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.user')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     ** path="/user/leave-organization",
     *   tags={"Web User"},
     *   summary="Leave the organization",
     *   operationId="Leave the organization",
     *   security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *   @OA\Parameter(
     *      name="id",
     *      in="query",
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

    public function leaveOrganization(Request $request)
    {
        try {
            $id = $request->id;
            \DB::beginTransaction();
            $user = $this->userService->leaveOrganization($id);

            if (empty($user)) {
                return $this->sendError(__('messages.failed_to_module_name', ['moduleName' => __('labels.leave_organization')]), ['general' => __('messages.failed_to_module_name', ['moduleName' => __('labels.leave_organization')])],  500, true);
            }
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.organization')]), $user, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_module_name', ['moduleName' => __('labels.leave_organization')]), ['general' => $e->getMessage()],  500, true);
        }
    }

    /**
     * @OA\Post(
     *       path="/user/{id}/profile-image",
     *       tags={"Web User"},
     *       summary = "To change the profile image of user",
     *       operationId = "To change the profile image of user",
     *       security={{"bearer_token":{}}, {"x_localization":{}}},
     *       @OA\Parameter(
     *           name="id",
     *           in="path",
     *           required=true,
     *           @OA\Schema(
     *               type="integer"
     *           )
     *       ),
     *       @OA\RequestBody(
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                   @OA\Property(
     *                       property="profile_image",
     *                       type="file",
     *                       description="Validations: filetype=jpg,jpeg,png, max=1MB",
     *                   ),
     *               ),
     *           ),
     *       ),
     *       @OA\Response(
     *           response=200,
     *           description="Success",
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *           )
     *       ),
     *       @OA\Response(
     *           response=401,
     *           description="Unauthorized"
     *       ),
     *       @OA\Response(
     *           response=400,
     *           description="Invalid request"
     *       ),
     *       @OA\Response(
     *           response=404,
     *           description="not found"
     *       ),
     * )
     */


    public function profileImage($id, ProfileImageRequest $request)
    {
        try {
            $data = $this->userService->details($id);
            if (empty($data)) {
                return null;
            }

            \DB::beginTransaction();
            $data = $this->userService->profileImage($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.profile_image')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.profile_image')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/user/assign-stack",
     * tags = {"Web User"},
     * summary = "To update role details",
     * operationId = "To assign stack to the users",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *    @OA\Parameter(
     *       name="user_id[]",
     *       in="query",
     *       description="user ids array",
     *       required= false,
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
     *
     *              @OA\Property(
     *                property="company_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="project_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="company_stack_modules_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="company_stack_category_id",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="stack_table_id",
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

    public function assignStacks(AssignStackRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->userService->assignStacks($request);
            \DB::commit();
            return $this->sendResponse(__('messages.visibility_permission_saved_successfully'), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_visibility_permission'), ['general' => $e->getMessage()], 500, true);
        }
    }
}
