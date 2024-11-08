<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordOtpRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordViaLinkRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Services\Admin\UserService;
use App\Models\Role;
use App\Models\LovPrivileges;
use App\Models\LovPrivilegeGroups;
//use Exception;

class LoginController extends BaseController
{
    use CommonTrait;

    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * @OA\Post(
     ** path="/admin/login",
     *   tags={"Login"},
     *   summary="Login",
     *   operationId="login",
     *
     *   @OA\Parameter(
     *      name="username",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
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
    public function authenticate(LoginRequest $request)
    {
        try {
            $postData = $request->all();
            $user     = $this->userService->getUserByEmail($postData['username']);
            if (empty($user)) {
                $user = $this->userService->getUserByPhoneNumber($postData['username']);
            }

            if (empty($user)) {
                return $this->sendError(trans('messages.sorry_invalid_email_and_or_password_combination'), ['general' => [trans('messages.sorry_invalid_email_and_or_password_combination')]], 404,true);
            }

            if ($user->is_active == 0) {
                $message = trans('messages.your_account_is_in_active_please_contact_admin');
                return $this->sendError($message, ['general' => [$message],'sss', $user], 400,true);
            }

            $token = auth()->attempt(['email' => $postData['username'], 'password' => $postData['password'], 'user_type' => 1]);
            if (!$token) {
                $token = auth()->attempt(['mobile_no' => $postData['username'], 'password' => $postData['password'], 'user_type' => 1]);
            }

            // Do auth
            if (!$token) {
                return $this->sendError(__('messages.unauthorized_login'), ["general" => __('messages.unauthorized_login')], 401, true);
            }

            $tokenType = 'bearer';
            $expiresIn = auth()->factory()->getTTL();
            return $this->sendResponse(__('messages.login_successfully'), compact('token', 'tokenType', 'expiresIn'), true);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_module_name', ['moduleName' => __('labels.login')]), ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     ** path="/admin/forgot-password",
     *   tags={"Login"},
     *   summary="Request link for forgot password",
     *   operationId="link for forgot password",
     *
     *   @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
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
    public function forgotPasswordOtp(ForgotPasswordOtpRequest $request)
    {
        try {
            $postData = $request->all();

            \DB::beginTransaction();
            $this->userService->setOtp($postData);
            \DB::commit();

            return $this->sendResponse(__('messages.reset_password_link_sent_to_your_email_id'), __('messages.reset_password_link_sent_to_your_email_id'), ['general' => __('messages.reset_password_link_sent_to_your_email_id')]);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_generate_module_name', ['moduleName' => __('labels.reset_password_link')]), ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     ** path="/admin/{otp}/reset-password",
     *   tags={"Login"},
     *   summary="Reset forgot password link for user.",
     *   operationId="reset-password",
     *
     *   @OA\Parameter(
     *      name="otp",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password_confirmation",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
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
    public function resetPasswordViaLink($otp, ResetPasswordViaLinkRequest $request)
    {
        try {
            $postData = $request->all();
            $postData['otp'] = base64_decode($otp);
            $otp      = $this->userService->checkOtpExists($postData['otp']);

            if (empty($otp)) {
                $message = __('messages.otp_entered_incorrect');
                return $this->sendError($message, ['general' => $message], 404, true);
            }
            $user = $this->userService->getUserByEmail($otp->email);
            if (empty($user)) {
                $user = $this->userService->getUserByPhoneNumber($otp->email);
            }

            if (empty($user)) {
                $message = __('messages.module_name_not_found', ['moduleName' => __('labels.user')]);
                return $this->sendError($message, ['general' => $message], 404, true);
            }
            if ($user->is_active != 1) {
                $message = __('messages.user_is_not_active_contact_admin');
                return $this->sendError($message, ['general' => $message], 404, true);
            }

            \DB::beginTransaction();
            $this->userService->setPassword($user, $postData['password']);
            $this->userService->deleteOtp($postData['otp']);
            \DB::commit();

            $message = __('messages.password_changed_successfully_please_login');
            return $this->sendResponse($message, $message, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.password')]), ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     ** path="/admin/change-password",
     *   tags={"Login"},
     *   summary="Change user password with token after login.",
     *   operationId="change-password",
     *   security={{"bearer_token":{}}, {"timezone":{}}},
     *
     *   @OA\Parameter(
     *      name="password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password_confirmation",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
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
    protected function changePassword(ChangePasswordRequest $request)
    {
        try {
            $postData  = $request->all();

            $user = $this->userService->getUserByEmail(\Auth::guard('api')->user()->email);
            if (empty($user)) {
                $message = __('messages.module_name_not_found', ['moduleName' => __('labels.user')]);
                return $this->sendError($message, ['general' => $message], 404);
            }

            \DB::beginTransaction();
            $this->userService->setPassword($user, $postData['password']);
            \DB::commit();

            $message = __('messages.module_name_updated_successfully', ['moduleName' => __('labels.password')]);
            return $this->sendResponse($message, $message);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.password')]), ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     ** path="/admin/me",
     *   tags={"Login"},
     *   summary="Get user profile base details after login.",
     *   operationId="my PRofile",
     *   security={{"bearer_token":{}}, {"timezone":{}}},
     *
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
    public function me()
    {
        try {
            $me = $this->userService->details(\Auth::user()->id);
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.user_profile')]), $me);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.user_profile')]), ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     ** path="/admin/logout",
     *   tags={"Login"},
     *   summary="Logout.",
     *   operationId="logout",
     *   security={{"bearer_token":{}}, {"x_localization":{}}},
     *
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
    public function logout()
    {
        try {
            auth()->logout();
            return $this->sendResponse(__('messages.logout_successful'), __('messages.logout_successful'), true);
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_logout'), ['general' => [$e->getMessage()]], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/{id}/update-profile",
     * tags = {"Login"},
     * summary = "To update profile",
     * operationId = "To update profile",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="for updating the selected profile",
     *      @OA\Schema(
     *           type="integer"
     *      )
     *     ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              required={"first_name","last_name","job_role","country_code","mobile_no","date_of_birth","start_date",},
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
     *                property="job_role",
     *                description = "Validation: min=2,max=50",
     *                type="string",
     *             ),
     *             @OA\Property(
     *                property="country_code",
     *                type="string",
     *                description="Validations: min=2",
     *             ),
     *             @OA\Property(
     *                property="mobile_no",
     *                description = "Validation: min=2,max=50",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="date_of_birth",
     *                description = "Validation: date-format=yyyy-mm-dd",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="start_date",
     *                description = "Validation: date-format=yyyy-mm-dd",
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

    public function updateProfile($id, UpdateProfileRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->userService->updateProfile($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.profile')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.profile')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
