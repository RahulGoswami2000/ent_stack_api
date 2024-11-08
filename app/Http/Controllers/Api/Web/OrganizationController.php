<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Company\LogoRequest;
use App\Http\Requests\Company\StoreUpdateRequest;
use App\Services\Web\OrganizationService;
use Exception;
use Illuminate\Http\Request;

class OrganizationController extends BaseController
{
    private $organizationService;

    public function __construct()
    {
        $this->organizationService = new OrganizationService;
    }

    /**
     * @OA\Post(
     * path="/organization/{id}/update",
     * tags = {"Organization"},
     * summary = "To update organization details",
     * operationId = "To update organization details",
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
     *             required={"company_name"},
     *              @OA\Property(
     *                property="company_name",
     *                description = "Validation: min=3,max=50",
     *                type="string",
     *             ),
     *              @OA\Property(
     *                property="url",
     *                description = "Validation: valid url",
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
            $data = $this->organizationService->details($id);
            if (empty($data)) {
                return null;
            }
            \DB::beginTransaction();
            $data = $this->organizationService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.company_settings')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.company_settings')]), ['general' => $e->getMessage()], 500, true);
        }
    }


    /**
     * @OA\Post(
     *    path="/organization/{id}/change-logo",
     *    tags={"Organization"},
     *    summary = "To change the logo of company",
     *    operationId = "To change the logo of company",
     *    security={{"bearer_token":{}}, {"x_localization":{}}},
     *          @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *     ),
     *   @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(
     *                      property="logo",
     *                      type="file",
     *                      description="Validations: filetype=jpg,jpeg,png, max=1MB",
     *                  ),
     *         ),
     *      ),
     *   ),
     *  @OA\Response(
     *        response=200,
     *        description="Success",
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

    public function changeLogo($id, LogoRequest $request)
    {
        try {
            $data = $this->organizationService->details($id);
            if (empty($data)) {
                return null;
            }

            \DB::beginTransaction();
            $data = $this->organizationService->changeLogo($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.company_settings')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.company_settings')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
