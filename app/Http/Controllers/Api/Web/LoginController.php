<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\CheckMyEmailRequest;
use App\Http\Requests\Auth\ForgotPasswordOtpRequest;
use App\Http\Requests\Auth\LoginAsCompanyRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordViaLinkRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\VerifyUserChangePasswordRequest;
use App\Services\Web\UserService;
use App\Traits\CommonTrait;
use Exception;
use Illuminate\Http\Request;

class LoginController extends BaseController
{
    use CommonTrait;

    private $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    /**
     * @OA\Post(
     ** path="/user/login",
     *   tags={"Web User"},
     *   summary="Login",
     *   operationId="Web login",
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
            $token    = auth()->attempt(['email' => $postData['username'], 'password' => $postData['password'], 'user_type' => 2]);
            if (!$token) {
                $token = auth()->attempt(['mobile_no' => $postData['username'], 'password' => $postData['password'], 'user_type' => 2]);
            }

            if (!$token) {
                return $this->sendError(__('messages.unauthorized_login'), ['general' => __('messages.unauthorized_login')], 401, true);
            }

            $tokenType = 'bearer';
            $expiresIn = auth()->factory()->getTTL();

            $user = $this->userService->details(\Auth::user()->id);

            return $this->sendResponse(__('messages.login_successfully'), compact('token', 'tokenType', 'expiresIn', 'user'), true);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_module_name', ['moduleName' => __('labels.login')]), ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     ** path="/user/forgot-password",
     *   tags={"Web User"},
     *   summary="Request link for forgot password",
     *   operationId="link for forgot password in web",
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
     ** path="/user/{otp}/reset-password",
     *   tags={"Web User"},
     *   summary="Reset forgot password link for user.",
     *   operationId="reset-password for web user",
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
     ** path="/user/change-password",
     *   tags={"Web User"},
     *   summary="Change user password with token after login.",
     *   operationId="change-password for web user",
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

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $postData = $request->all();
            $user     = $this->userService->getUserByEmail(\Auth::user()->email);
            if (empty($user)) {
                $message = __('messages.module_name_not_found', ['moduleName' => __('labels.user')]);
                return $this->sendError($message, ['general' => $message], 404);
            }

            \DB::beginTransaction();
            $this->userService->setPassword($user, $postData['password']);
            \DB::commit();

            $message = __('messages.module_name_changed_successfully', ['moduleName' => __('labels.password')]);
            return $this->sendResponse($message, $message, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_module_name', ['moduleName' => __('labels.password')]), ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     ** path="/user/logout",
     *   tags={"Web User"},
     *   summary="Logout.",
     *   operationId="logout for web user",
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
     * path="/user/{id}/update-profile",
     * tags = {"Web User"},
     * summary = "To update profile",
     * operationId = "To update profile for web user",
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
     *              required={"first_name","last_name","job_role","country_code","mobile_no","date_of_birth","start_date","email"},
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
     *              @OA\Property(
     *                property="email",
     *                description = "follow valid email format",
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
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.profile')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.profile')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     ** path="/user/me",
     *   tags={"Web User"},
     *   summary="Get user profile base details after login.",
     *   operationId="my PRofile for web user",
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

    public function me(Request $request)
    {
        try {
            if (\Auth::user()->user_type == 1 && empty($request->company_id)) {
                $message = __('validation.required.select', ['attribute' => __('labels.company')]);
                $this->sendError($message, $message, 400, true);
            }

            if (\Auth::user()->client_assigned == 0 && empty($this->userService->checkUserIsAssociatedWithCompany(\Auth::user()->id, $request->company_id))) {
                $message = __('messages.you_are_not_associated_with_this_company');
                $this->sendError($message, $message, 400, true);
            }

            $me = $this->userService->details(\Auth::user()->id, $request->company_id);
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.user_profile')]), $me);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.user_profile')]), ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     ** path="/user/check-my-email",
     *   tags={"Web User"},
     *   summary="Check user profile with email.",
     *   operationId="Check user profile with email.",
     *   security={{"timezone":{}}},
     *
     *   @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
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

    public function checkMyEmail(CheckMyEmailRequest $request)
    {
        try {
            $user = $this->userService->getUserByEmail($request->email);

            if (!empty($user->password)) {
                return $this->sendError(__('messages.user_is_already_verified_please_login'), ['general' => __('messages.user_is_already_verified_please_login')], 206, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.user_profile')]), $user);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.user_profile')]), ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     ** path="/user/verify-user-change-password",
     *   tags={"Web User"},
     *   summary="Verify user and Change password.",
     *   operationId="verify-user-change-password for web user",
     *   security={{"timezone":{}}},
     *
     *   @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
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

    public function verifyUserChangePassword(VerifyUserChangePasswordRequest $request)
    {
        try {
            $postData = $request->all();
            $user     = $this->userService->getUserByEmail($postData['email']);
            if (empty($user)) {
                $message = __('messages.module_name_not_found', ['moduleName' => __('labels.user')]);
                return $this->sendError($message, ['general' => $message], 404, true);
            }

            \DB::beginTransaction();
            $this->userService->verifyUserChangePassword($user, $postData['password']);
            \DB::commit();

            $message = __('messages.password_changed_successfully_please_login');
            return $this->sendResponse($message, $message, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_module_name', ['moduleName' => __('labels.password')]), ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     ** path="/user/login-as-company",
     *   tags={"Web User"},
     *   summary="To login as company",
     *   operationId="To login as company",
     *   security={{"timezone":{}}},
     *
     *   @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="company_id",
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

    public function loginAsCompany(LoginAsCompanyRequest $request)
    {
        try {
            $userDetails = $this->userService->getUserByEmail($request->email);
            if (empty($userDetails)) {
                $message = __('messages.module_name_not_found', ['moduleName' => __('labels.user')]);
                return $this->sendError($message, ['general' => $message], 404, true);
            }

            if ($userDetails->user_type == 1 && empty($request->company_id)) {
                $message = __('validation.required.select', ['attribute' => __('labels.company')]);
                return $this->sendError($message, $message, 400, true);
            }

            if ($userDetails->id != 1 && $userDetails->client_assigned == 0 && empty($this->userService->checkUserIsAssociatedWithCompany($userDetails->id, $request->company_id))) {
                $message = __('messages.you_are_not_associated_with_this_company');
                return $this->sendError($message, $message, 400, true);
            }

            $token = auth()->login($userDetails);
            if (!$token) {
                return $this->sendError(__('messages.unauthorized_login'), ['general' => __('messages.unauthorized_login')], 401, true);
            }

            $tokenType = 'bearer';
            $expiresIn = auth()->factory()->getTTL();

            $user = $this->userService->details($userDetails->id, null);

            return $this->sendResponse(__('messages.login_successfully'), compact('token', 'tokenType', 'expiresIn', 'user'), true);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_module_name', ['moduleName' => __('labels.login')]), ['general' => $e->getMessage()], 500);
        }
    }
}
