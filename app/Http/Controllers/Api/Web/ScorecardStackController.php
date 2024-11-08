<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\ScorecardStack\StoreUpdateRequest;
use App\Http\Requests\ScorecardStack\UpdateScorecardNodeDataRequest;
use App\Services\Web\ScorecardStackService;
use Exception;
use Illuminate\Http\Request;

class ScorecardStackController extends BaseController
{

    private ScorecardStackService $scorecardStackService;

    public function __construct()
    {
        $this->scorecardStackService = new ScorecardStackService;
    }
    /**
     * @OA\Get(
     * path="/scorecard-stack",
     * tags = {"Scorecard Stack"},
     * summary = "To get the list of scorecard stack",
     * operationId = "To get the list of scorecard stack",
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
            $listData          = $this->scorecardStackService->list();
            $count             = 0;
            $rows              = [];
            if (!empty($listData) && isset($listData['count']) && isset($listData['data'])) {
                list($count, $rows) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.scorecard_stack')]), compact('count', 'rows'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.scorecard_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/scorecard-stack/create",
     * tags = {"Scorecard Stack"},
     * summary = "To add scorecard stack",
     * operationId = "To add scorecard stack",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *
     *              @OA\Property(
     *                property="company_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="project_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="project_category_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="scorecard_type",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="scorecard_start_from",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="scorecard_data",
     *                type="json",
     *                description="Validation : json",
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

    public function store(StoreUpdateRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->scorecardStackService->store($request);
            \DB::commit();

            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.scorecard_stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.scorecard_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/scorecard-stack/details",
     * tags = {"Scorecard Stack"},
     * summary = "To check scorecard-stack details",
     * operationId = "To check scorecard-stack details",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              @OA\Property(
     *                property="company_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="project_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="company_stack_modules_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="company_stack_category_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="from_date",
     *                type="string",
     *                description="Validation : date-format=yyyy-mm-dd",
     *             ),
     *              @OA\Property(
     *                property="to_date",
     *                type="string",
     *                description="Validation : date-format=yyyy-mm-dd",
     *             ),
     *              @OA\Property(
     *                property="type",
     *                type="string",
     *                description="GraphView",
     *             ),
     *         ),
     *      ),
     *   ),
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

    public function details(Request $request)
    {
        try {
            $data = $this->scorecardStackService->details($request);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.scorecard_stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.scorecard_stack')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.scorecard_stack')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.scorecard_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/scorecard-stack/{id}/update",
     * tags = {"Scorecard Stack"},
     * summary = "To update scorecard-stack details",
     * operationId = "To update scorecard-stack details",
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
     *                property="company_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="project_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="project_category_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="scorecard_type",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="scorecard_start_from",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="scorecard_data",
     *                type="json",
     *                description="Validation : json",
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
            $data = $this->scorecardStackService->details($request);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.scorecard_stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.scorecard_stack')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->scorecardStackService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.scorecard_stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.scorecard_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Delete(
     * path="/scorecard-stack/{id}/delete",
     * tags = {"Scorecard Stack"},
     * summary = "To delete scorecard-stack",
     * operationId = "To delete scorecard-stack",
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
            $data = $this->scorecardStackService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.scorecard_stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.scorecard_stack')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->scorecardStackService->destory($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.scorecard_stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.scorecard_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/scorecard-stack/save",
     * tags = {"Scorecard Stack"},
     * summary = "To save Project scorecard Stack ",
     * operationId = "To save Project scorecard stack",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              @OA\Property(
     *                property="company_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="project_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="company_stack_modules_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="company_stack_category_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="scorecard_type",
     *                type="integer",
     *                description="(1 = weekly, 2 = bi-weekly, 3 = semi-monthly, 4 = monthly, 5 = quarterly, 6 = annually)",
     *             ),
     *              @OA\Property(
     *                property="scorecard_start_from",
     *                type="integer",
     *                description="(0 = sunday, 1 = monday, 2 = tuesday, 3 = wednesday, 4 = thursday, 5 = friday, 6 = saturday)",
     *             ),
     *              @OA\Property(
     *                property="scorecard_data",
     *                type="json",
     *                description="Validation : json",
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

    public function save(StoreUpdateRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->scorecardStackService->save($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.scorecard_stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.scorecard_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/scorecard-stack/node-entry",
     * tags = {"Scorecard Stack"},
     * summary = "To enter node entry Project scorecard Stack",
     * operationId = "To enter node entry Project scorecard Stack",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              @OA\Property(
     *                property="scorecard_stack_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="node_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *             @OA\Property(
     *                property="assigned_color",
     *                type="text",
     *                description="Validation : text",
     *             ),
     *              @OA\Property(
     *                property="value",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="from_date",
     *                type="string",
     *                description="Validation : Date",
     *             ),
     *              @OA\Property(
     *                property="to_date",
     *                type="string",
     *                description="Validation : Date",
     *             ),
     *              @OA\Property(
     *                property="comment",
     *                type="string",
     *                description="Validation : text",
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

    public function nodeEntry(Request $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->scorecardStackService->details($request->scorecard_stack_id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.scorecard_stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.scorecard_stack')])], 404, true);
            }
            $data = $this->scorecardStackService->nodeEntry($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.scorecard_stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.scorecard_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }


    /**
     * @OA\Post(
     * path="/scorecard-stack/change-value",
     * tags = {"Scorecard Stack"},
     * summary = "To change the value Project scorecard Stack",
     * operationId = "To change the value Project scorecard Stack",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              required={"scorecard_node_data_id","value"},
     *              @OA\Property(
     *                property="scorecard_node_data_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="value",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="from_date",
     *                type="string",
     *                description="Validation : integer",
     *             ),
     *             @OA\Property(
     *                property="assigned_color",
     *                type="text",
     *                description="Validation : text",
     *             ),
     *              @OA\Property(
     *                property="to_date",
     *                type="string",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="comment",
     *                type="string",
     *                description="Validation : text",
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

    public function updateScorecardNodeData(UpdateScorecardNodeDataRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->scorecardStackService->updateScorecardNodeData($request);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.scorecard_stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.scorecard_stack')])], 404, true);
            }
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.scorecard_stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.scorecard_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
