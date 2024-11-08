<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\TeamStack\StoreUpdateRequest;
use App\Http\Requests\TeamStack\AssignStackRequest;
use App\Models\Company;
use App\Models\User;
use App\Services\Web\TeamStackService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class TeamStackController extends BaseController
{
    private $teamStackService;

    public function __construct()
    {
        $this->teamStackService = new TeamStackService;
    }

    /**
     * @OA\Get(
     * path="/team-stack",
     * tags = {"Team Stack"},
     * summary = "To get the list of team stack",
     * operationId = "To get the list of team stack",
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
            $listData = $this->teamStackService->list();
            $count = 0;
            $row = [];
            if (!empty($listData) && isset($listData['count']) && isset($listData['data'])) {
                list($count, $row) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.team_stack')]), compact('count', 'row'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.team_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }
    /**
     * @OA\Post(
     * path="/team-stack/create",
     * tags = {"Team Stack"},
     * summary = "To add Project Team Stack ",
     * operationId = "To add Project team stack",
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
     *                property="project_category_id",
     *                type="integer",
     *                description="Validation : integer",
     *             ),
     *              @OA\Property(
     *                property="team_stack_data[]",
     *                type="array",
     *                @OA\Items(
     *                  type="integer",
     *                ),
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
            $data = $this->teamStackService->store($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.team_stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.team_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/team-stack/details",
     * tags = {"Team Stack"},
     * summary = "To check the details Project Team Stack ",
     * operationId = "To check the details Project team stack",
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
     *                property="team_stack_data[]",
     *                type="array",
     *                @OA\Items(
     *                  type="integer",
     *                ),
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
    public function details(StoreUpdateRequest $request)
    {
        try {
            $data = $this->teamStackService->details($request);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.team_stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.team_stack')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.team_stack')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.team_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/team-stack/{id}/update",
     * tags = {"Team Stack"},
     * summary = "To update team stack details ",
     * operationId = "To update team stack details",
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
     *                property="team_stack_data[]",
     *                type="array",
     *                @OA\Items(
     *                  type="integer",
     *                ),
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
            $data = $this->teamStackService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.team_stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.team_stack')])], 404, true);
            }

            \DB::beginTransaction();
            $data = $this->teamStackService->update($id, $request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_updated_successfully', ['moduleName' => __('labels.team_stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.team_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }
    /**
     * @OA\Delete(
     * path="/team-stack/{id}/delete",
     * tags = {"Team Stack"},
     * summary = "To delete team stack ",
     * operationId = "To delete team stack",
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
            $data = $this->teamStackService->details($id);

            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.team_stack')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.team_stack')])], 404, true);
            }
            \DB::beginTransaction();
            $data = $this->teamStackService->destory($id);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.team_stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.team_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/team-stack/save",
     * tags = {"Team Stack"},
     * summary = "To save Project Team Stack ",
     * operationId = "To save Project team stack",
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
     *                property="team_stack_data[]",
     *                type="array",
     *                @OA\Items(
     *                  type="integer",
     *                ),
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
            $data = $this->teamStackService->save($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.team_stack')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.team_stack')]), ['general' => $e->getMessage()], 500, true);
        }
    }
}
