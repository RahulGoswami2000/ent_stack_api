<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\TemplateService;
use App\Http\Requests\Template\StoreUpdateRequest;
use App\Http\Requests\Template\ChangeStatusRequest;
use Exception;
use Illuminate\Http\Request;

class TemplateController extends BaseController
{

    private $templateService;

    public function __construct()
    {
        $this->templateService = new TemplateService;
    }
    /**
     * @OA\Post(
     * path="/admin/template",
     * tags = {"Template"},
     * summary = "To get the list,TEMPLATE_INDEX",
     * operationId = "To get the list of template",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *       @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              required={"type"},
     *             @OA\Property(
     *                property="type",
     *                type="string",
     *                description="Validation Type : email",
     *            ),
     *              @OA\Property(
     *                property="name",
     *                type="string",
     *                description="Using For Search By Name",
     *            ),
     *           ),
     *         ),
     *       ),
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
     *      )
     */

    public function index(Request $request)
    {

        try {
            $postData          = $request->all();
            $pageNumber        = !empty($postData['page']) ? $postData['page'] : 1;
            $pageLimit         = !empty($postData['per_page']) ? $postData['per_page'] : 100;
            $skip              = ($pageNumber - 1) * $pageLimit;
            $listData          = $this->templateService->list($postData, $skip, $pageLimit);
            $count             = 0;
            $rows              = [];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['data']) && isset($listData['count'])) {
                list($rows, $count) = array_values($listData);
            }
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.email')]), compact('count', 'rows'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.email')]), ['general' => $e->getMessage()], 500, true);
        }
    }
    /**
     * @OA\Get(
     *   path="/admin/template/{id}/details",
     *   tags={"Template"},
     *   summary="to get template details,TEMPLATE_DETAILS",
     *   operationId="template-details",
     *   security={{"bearer_token":{}}, {"x_localization":{}}},
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
            $data = $this->templateService->details($id);
            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.email')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.email')])], 404, true);
            }
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.email')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.email')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/template/{id}/update",
     * tags = {"Template"},
     * summary = "To update template details,TEMPLATE_UPDATE",
     * operationId = "To update template details",
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
     *       @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              @OA\Property(
     *                property="template_url",
     *                type="string",
     *                description="Validations:Url",
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
            $data = $this->templateService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.email')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.email')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->templateService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.email')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.email')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/admin/template/{id}/change-status",
     * tags = {"Template"},
     * summary = "To change status of template,TEMPLATE_CHANGE_STATUS",
     * operationId = "To change status of template",
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
     *          description="Validations:0,1",
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
            $data = $this->templateService->details($id);
            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.email')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.email')])], 404, true);
            }
            if (empty($data->api_url)) {
                return $this->sendError(__('messages.url_is_required_after_change_status_template', ['moduleName' => __('labels.email')]), ['general' => __('messages.url_is_required_after_change_status_template', ['moduleName' => __('labels.email')])], 404, true);
            }

            $data = $this->templateService->changeStatus($id, $request);
            if ($data->is_active == 1) {
                $is_active = 'activated';
            } else {
                $is_active = 'deactivated';
            }
            \DB::commit();
            return $this->sendResponse(__('messages.email_module_changed_successfully', ['module' => __('labels.email'), 'moduleName' => $is_active]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_change_status', ['moduleName' => __('labels.email')]), ['general' => $e->getMessage()], 500, true);
        }
    }
    public function setUpdate()
    {
        $data = $this->template->find(2);

        $data->update([
            'api_url'   => 'test.com',
            'is_active' => 0,
            'type' => 'test'
        ]);
    }
}
