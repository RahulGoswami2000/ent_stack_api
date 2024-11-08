<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Subscription\PaymentRequest;
use App\Http\Requests\ApiIntegration\RegistrationRequest;
use App\Services\Admin\ApiIntegrationService;
use Exception;
use Illuminate\Http\Request;

class ApiIntegrationController extends BaseController
{
    private ApiIntegrationService $apiIntegrationService;

    public function __construct()
    {
        $this->apiIntegrationService = new ApiIntegrationService;
    }
    /**
     * @OA\Get(
     *    path="/api-integration/registration",
     *    tags={"Api Integration"},
     *    summary = "Registration",
     *    operationId = "registration",
     *
     *   @OA\Parameter(
     *      name="first_name",
     *      in="query",
     *      required=true,
     *      description="Validations: min=3, max=50",
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="last_name",
     *      in="query",
     *      required=true,
     *      description="Validations: min=3, max=50",
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="mobile_no",
     *      in="query",
     *      required=true,
     *      description="Validations: min=4, max=20 regex:/^[0-9]+$/",
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      description="Validations: min=3, max=70",
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password",
     *      in="query",
     *      required=true,
     *      description="Validations: min=8, max=20",
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="refer_code",
     *      in="query",
     *      description="Unique refer code",
     *      @OA\Schema(
     *           type="string"
     *      )
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

    public function store(RegistrationRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->apiIntegrationService->store($request);
            if (empty($data)) {
                return $this->sendResponse(__('messages.wrong_referal_code', ['moduleName' => __('labels.registration')]), $data, true);
            }
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.registration')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.registration')]), ['general' => $e->getMessage()], 500, true);
        }
    }

}
