<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Archive\StoreUpdateRequest;
use App\Services\Web\ScorecardStackArchiveService;
use Exception;
use Illuminate\Http\Request;

class ScorecardStackArchiveController extends BaseController
{
    private $scorecardStackArchiveService;

    public function __construct()
    {
        $this->scorecardStackArchiveService = new ScorecardStackArchiveService;
    }

    /**
     * @OA\Post(
     * path="/archive",
     * tags = {"Archive"},
     * summary = "To get the list of Archived list",
     * operationId = "To get the list of Archived list",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *    @OA\RequestBody(
     *       @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="export_type", type="string", description="csv, xlsx"),
     *               @OA\Property(property="filter_data", type="object",
     *                      @OA\Property(property="name", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                      @OA\Property(property="type", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                      @OA\Property(property="id", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *                      @OA\Property(property="created_at", type="object",
     *                               @OA\Property(property="filterType", type="string"),
     *                               @OA\Property(property="type", type="string"),
     *                               @OA\Property(property="filter", type="string")
     *                      ),
     *               ),
     *               @OA\Property(property="sort_data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="colId", type="string"),
     *                      @OA\Property(property="sort", type="string")
     *                  )
     *              ),
     *              @OA\Property(property="per_page", type="integer"),
     *              @OA\Property(property="page", type="integer"),
     *            )
     *        )
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

    public function index(Request $request)
    {
        try {
            $postData          = $request->all();
            $pageNumber        = !empty($postData['page']) ? $postData['page'] : 1;
            $pageLimit         = !empty($postData['per_page']) ? $postData['per_page'] : 100;
            $skip              = ($pageNumber - 1) * $pageLimit;
            $listData          = $this->scorecardStackArchiveService->list($postData, $skip, $pageLimit);
            $count             = 0;
            $rows              = [];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['data']) && isset($listData['count'])) {
                list($rows, $count) = array_values($listData);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.scorecard_archived')]), compact('count', 'rows', 'downloadExportUrl'), false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.scorecard_archived')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/archive/save",
     * tags = {"Archive"},
     * summary = "To add into Archive",
     * operationId = "To add into Archive",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     * 
     *    
     *      @OA\RequestBody(
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             required={"company_id","type","project_id","company_stack_modules_id","company_stack_category_id","scorecard_stack_id"},
     *              @OA\Property(
     *                property="company_id",
     *                description = "Existing company",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="type",
     *                description="1 = Full Stack and 2= Metric",
     *                type="integer",
     *             ),
     *              @OA\Property(
     *                property="project_id",
     *                description="Existing project",
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
     *             @OA\Property(
     *                property="scorecard_stack_id",
     *                type="integer",
     *             ),
     *             @OA\Property(
     *                property="node_id",
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

    public function save(StoreUpdateRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->scorecardStackArchiveService->create($request);
            \DB::commit();

            return $this->sendResponse(__('messages.module_name_saved_successfully', ['moduleName' => __('labels.scorecard_archived')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.scorecard_archived')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     * path="/archive/{id}/details",
     * tags = {"Archive"},
     * summary = "To check archived scorecard-stack details",
     * operationId = "To check archived scorecard-stack details",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *    @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *     ),     
     * 
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
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

    public function details($id, Request $request)
    {
        try {
            $data = $this->scorecardStackArchiveService->details($id, $request);

            if (empty($data)) {
                return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.scorecard_archived')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.scorecard_archived')])], 404, true);
            }

            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.scorecard_archived')]), $data, false);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.scorecard_archived')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Delete(
     * path="/archive/{id}/restore",
     * tags = {"Archive"},
     * summary = "To restore the archived",
     * operationId = "To restore the archived",
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

    public function restore($id)
    {
        try {
            \DB::beginTransaction();
            $data = $this->scorecardStackArchiveService->restore($id);
            if (empty($data)) {
                return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.scorecard_archived')]), ['general' => __('messages.module_name_not_found', ['moduleName' => __('labels.scorecard_archived')])], 404, true);
            }

            \DB::commit();
            return $this->sendResponse(__('messages.module_name_deleted_successfully', ['moduleName' => __('labels.scorecard_archived')]), $data, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError(__('messages.failed_to_delete_module_name', ['moduleName' => __('labels.scorecard_archived')]), ['general' => $e->getMessage(),$e->getLine()], 500, true);
        }
    }
}
