<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\GoalStack\StoreUpdateRequest;
use App\Services\Web\GoalStackService;
use Exception;
use Illuminate\Http\Request;

class GoalStackController extends BaseController
{
    private $goalStackService;

    public function __construct()
    {
        $this->goalStackService = new GoalStackService;
    }

    /**
     * @OA\Get(
     * path="/goal-stack",
     * tags = {"Goal Stack"},
     * summary = "To get the list of Goal Stack",
     * operationId = "To get the list of Goal Stack",
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
            $listData          = $this->goalStackService->list();
            $count             = 0;
            $rows              = [];
            if (!empty($listData) && isset($listData['count']) && isset($listData['data'])) {
                list($rows, $count) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.goal_stack')]), compact('count', 'rows'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.goal_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/goal-stack/details",
     * tags = {"Goal Stack"},
     * summary = "To check Goal Stack details",
     * operationId = "To check Goal Stack details",
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

    public function details(StoreUpdateRequest $request)
    {
        try {
            $data = $this->goalStackService->details($request);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.goal_stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.goal_stack')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.goal_stack')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.goal_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Delete(
     * path="/goal-stack/{id}/delete",
     * tags = {"Goal Stack"},
     * summary = "To delete Goal Stack",
     * operationId = "To delete Goal Stack",
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
            $data = $this->goalStackService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.goal_stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.goal_stack')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->goalStackService->destroy($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.goal_stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.goal_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/goal-stack/save",
     * tags = {"Goal Stack"},
     * summary = "To save Project Goal Stack ",
     * operationId = "To save Project Goal stack",
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
     *                property="stack_data",
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
            $data = $this->goalStackService->save($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.goal_stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.goal_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
