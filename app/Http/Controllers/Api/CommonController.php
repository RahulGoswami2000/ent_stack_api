<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Common\DeleteFileRequest;
use App\Http\Requests\Common\FileUploadRequest;
use App\Library\FunctionUtils;
use App\Services\CommonService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CommonController extends BaseController
{
    private CommonService $commonService;

    public function __construct()
    {
        $this->commonService = new CommonService;
    }

    /**
     * @OA\Post(
     ** path="/sync",
     *   tags={"Common"},
     *   summary="Sync Data",
     *   operationId="common-sync-data",
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
    public function iterateKeys(&$inputArray, $tmp = null, $name = '')
    {
        if ($tmp === null) {
            $tmp = $inputArray;
        }

        foreach ($tmp as $index => $value) {
            if (is_array($value)) {
                $this->iterateKeys($inputArray, $value, $name . '_' . $index);
            } else {
                $translation = $value;
                if (\Str::contains($translation, ':')) {
                    $arr = [];
                    preg_match_all('/\B:\w+/i', $translation, $arr);
                    $value = $translation;
                    if (sizeof($arr) > 0) {
                        foreach ($arr[0] as $v) {
                            $cleanMatchedStr = str_replace(':', '', $v);
                            $value           = str_replace($v, '{{' . $cleanMatchedStr . '}}', $value);
                        }
                    }
                    $translation = $value;
                }

                $inputArray[$name . '_' . $index] = $translation;
            }

            if (isset($inputArray[$index])) {
                unset($inputArray[$index]);
            }
        }

        return $inputArray;
    }


    public function sync(Request $request)
    {
        try {
            $files = collect(\File::files(resource_path('lang/en/')));

            $translationData = $files->reduce(function ($trans, $file) {
                $translations = require($file);
                $trans[]      = $this->iterateKeys($translations, null, str_replace(".php", "", basename($file)));
                return $trans;
            }, []);
            $translationData = array_merge(...$translationData);
            $formatOfMatrix  = array_values(config('global.FORMAT_OF_MATRIX'));
            $scorecardType   = array_values(config('global.SCORECARD_TYPE'));
            $templateEmail   = array_values(config('global.MAIL_TEMPLATE'));
            $roleType        = array_values(config('global.ROLE_TYPE'));
            $status          = array_values(config('global.STATUS'));
            $referralStatus  = array_values(config('global.REFERRAL_STATUS'));
            $days            = array_values(config('global.DAYS'));
            $stackModuleList = $this->commonService->stackModuleList();

            return $this->sendResponse(__('messages.synced_successfully'), compact('formatOfMatrix', 'translationData', 'scorecardType', 'templateEmail', 'roleType', 'status', 'stackModuleList', 'referralStatus', 'days'), false);
        } catch (\Exception $e) {
            return $this->sendError(__('messages.failed_to_sync'), ["general" => [$e->getMessage()]], 500, true);
        }
    }

    /**
     * @OA\Get(
     ** path="/language/{local_key}",
     *   tags={"Common"},
     *   summary="Language translation Data for web",
     *   operationId="common-language-translation-data-for-web",
     *
     *   @OA\Parameter(
     *      name="local_key",
     *      in="path",
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
    public function languageTranslationData($localKey)
    {
        try {
            $files = collect(\File::files(resource_path('lang/' . $localKey . '/')));

            $translationData = $files->reduce(function ($trans, $file) {
                $translations = require($file);
                $trans[]      = $this->iterateKeys($translations, null, str_replace(".php", "", basename($file)));
                return $trans;
            }, []);
            $translationData = array_merge(...$translationData);

            return response()->json($translationData, 200);
        } catch (\Exception $e) {
            return $this->sendError('Failed to get language translation data for web', ["general" => [$e->getMessage()]], 500, true);
        }
    }

    public function callManualCommand(Request $request)
    {
        if (!empty($request->my_command)) {
            try {
                $result['status'] = 200;
                foreach ($request->my_command as $item) {
                    \Artisan::call($item, []);
                }
                $result['message'] = "Command Run successfully.";
            } catch (\Throwable $e) {
                $result['status'] = 422;
                $result['errors'] = $e->getMessage();
            }
            return response()->json($result, $result['status']);
        }
        return response()->json(['status' => 200, 'message' => "Not Found."]);
    }

    public function viewLog(Request $request)
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            if (!empty($request->clear)) {
                exec('echo "" > ' . $logPath);
            }

            $result['status']  = 200;
            $result['message'] = file_get_contents($logPath);
        } catch (\Throwable $e) {
            $result['status'] = 422;
            $result['errors'] = $e->getMessage();
        }
        return response()->json(['status' => 200, 'message' => $result]);
    }

    /**
     * @OA\Get(
     * path="/metric-list",
     * tags = {"Common"},
     * summary = "To get the metric dropdown data",
     * operationId = "To get the metric dropdown data",
     *
     *      @OA\Parameter(
     *      name="type",
     *      in="query",
     *      description="type=empty gives all results, type = 1 gives the result category wise and type = 2 gives result where metric_category_id is null in database",
     *      required=false,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *     ),
     *      @OA\Parameter(
     *      name="metric_type",
     *      in="query",
     *      description="metric_type=empty gives all results, metric_type = 1 single and metric_type = 2 calculation",
     *      required=false,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *     ),
     *      @OA\Parameter(
     *      name="metric_category_id",
     *      description="metric_category_id is required when you assign type = 1",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *     ),
     *      @OA\Parameter(
     *      name="company_id",
     *      description="give the company id here",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
     *      @OA\Parameter(
     *      name="search",
     *      description="search metric name",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
     *     @OA\Parameter(
     *      name="access_type",
     *      description="0 for web and 1 for admin",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *     ),
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


    public function metric(Request $request)
    {
        try {
            \DB::beginTransaction();
            $listData          = $this->commonService->metric($request);
            $count             = 0;
            $rows              = [];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['data']) && isset($listData['count'])) {
                list($rows, $count) = array_values($listData);
            }
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.metric')]), compact('count', 'rows', 'downloadExportUrl'), false);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/metric-group-list",
     * tags = {"Common"},
     * summary = "To get the metricgroup dropdown data",
     * operationId = "To get the metricgroup dropdown data",
     *
     *      @OA\Parameter(
     *      name="type",
     *      in="query",
     *      description="type=empty gives all results, type = 1 gives the result category wise and type = 2 gives result where metric_category_id is null in database",
     *      required=false,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *     ),
     *      @OA\Parameter(
     *      name="metric_category_id",
     *      description="metric_category_id is required when you assign type = 1",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *     ),
     *      @OA\Parameter(
     *      name="search",
     *      description="search metric group name",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
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

    public function metricGroup(Request $request)
    {
        try {
            \DB::beginTransaction();
            $pageData          = $request->all();
            $pageNumber        = !empty($pageData['page']) ? $pageData['page'] : 1;
            $pageLimit         = !empty($pageData['per_page']) ? $pageData['per_page'] : 100;
            $skip              = ($pageNumber - 1) * $pageLimit;
            $listData          = $this->commonService->metricGroup($pageData, $skip, $pageLimit);
            $count             = 0;
            $rows              = [];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['data']) && isset($listData['count'])) {
                list($rows, $count) = array_values($listData);
            }
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.metric_group')]), compact('count', 'rows', 'downloadExportUrl'), false);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric_group')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/metric-category-list",
     * tags = {"Common"},
     * summary = "To get the metric category dropdown data",
     * operationId = "To get the metric category dropdown data",
     *
     *      @OA\Parameter(
     *      name="search",
     *      description="search metric category name",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
     *     @OA\Parameter(
     *      name="access_from",
     *      description="0 for web and 1 for admin",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
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

    public function metricCategory(Request $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->commonService->category($request);
            if ($request->access_from == 0) {
                $newdata[] = ['id' => -1, 'name' => 'All'];
                $newdata[] = ['id' => null, 'name' => 'Custom'];
                $data = array_merge($newdata, array_values($data));
            }
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.metric_category')]), $data, false);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.metric_category')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/roles-list",
     * tags = {"Common"},
     * summary = "To get the roles dropdown data",
     * operationId = "To get the roles category dropdown data",
     *
     *      @OA\Parameter(
     *      name="search",
     *      description="search role name",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
     *      @OA\Parameter(
     *      name="role_type",
     *      description="pass the value role_type= 1(Admin)  or role_type = 2(Web)",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
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

    public function mstRoles(Request $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->commonService->roles($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.roles')]), $data, false);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.roles')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/user-list",
     * tags = {"Common"},
     * summary = "To get the user dropdown data",
     * operationId = "To get the user category dropdown data",
     *
     *      @OA\Parameter(
     *      name="search",
     *      description="search role name, email",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
     *      @OA\Parameter(
     *      name="user_type",
     *      description="pass the value role_type= 1(Admin)  or role_type = 2(Web)",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
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

    public function users(Request $request)
    {
        try {
            \DB::beginTransaction();
            $pageData          = $request->all();
            $pageNumber        = !empty($pageData['page']) ? $pageData['page'] : 1;
            $pageLimit         = !empty($pageData['per_page']) ? $pageData['per_page'] : 100;
            $skip              = ($pageNumber - 1) * $pageLimit;
            $listData          = $this->commonService->user($pageData, $skip, $pageLimit);
            $count             = 0;
            $rows              = [];
            $downloadExportUrl = "";
            if (!empty($listData) && isset($listData['data']) && isset($listData['count'])) {
                list($rows, $count) = array_values($listData);
            }
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.user')]), compact('count', 'rows', 'downloadExportUrl'), false);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.user')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/privileges-list",
     * tags = {"Common"},
     * summary = "To get the list of privileges",
     * operationId = "To get the list of privileges",
     * security={{"bearer_token":{}}, {"x_localization":{}}},
     *
     *       @OA\Parameter(
     *          name="menu_type",
     *          description="pass the value menu_type= 1(Admin)  or menu_type = 2(Web)",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *           type="string"
     *          )
     *     ),
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
    public function privilegesList(Request $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->commonService->privilegesList($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.privileges')]), $data, false);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.privileges')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/company-list",
     * tags = {"Common"},
     * summary = "To get the company dropdown data",
     * operationId = "To get the company dropdown data",
     *
     *      @OA\Parameter(
     *      name="search",
     *      description="search company name",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
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

    public function companyList(Request $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->commonService->companyList($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.company')]), $data, false);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.company')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/webuser-list",
     * tags = {"Common"},
     * summary = "To get the web user",
     * operationId = "To get the web user",
     *
     *      @OA\Parameter(
     *      name="search",
     *      description="search user name and email",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
     *      @OA\Parameter(
     *      name="company_id",
     *      description="enter the company id for which company you want to find the user list",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
     *      @OA\Parameter(
     *       name="role_ids[]",
     *       in="query",
     *       description="User role ids array",
     *       required= false,
     *       @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *           type="integer"
     *         )
     *       ),
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

    public function webUserList(Request $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->commonService->webUserList($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.user')]), $data, false);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.user')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/stackmodule-list",
     * tags = {"Common"},
     * summary = "To get the stackmodule list",
     * operationId = "To get the stackmodule list",
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

    public function stackModuleList()
    {
        try {
            \DB::beginTransaction();
            $data = $this->commonService->stackModuleList();
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.stack_module')]), $data, false);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.stack_module')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Get(
     * path="/web-metric-and-metric-group-list",
     * tags = {"Common"},
     * summary = "To get the web metric and metric group list",
     * operationId = "To get the web metric and metric group list",
     *
     *      @OA\Parameter(
     *      name="metric_category_id",
     *      description="give the category id here",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
     *      @OA\Parameter(
     *      name="company_id",
     *      description="give the company id here",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *     ),
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

    public function metricAndMetricGroupList(Request $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->commonService->metricAndMetricGroupList($request);
            \DB::commit();
            return $this->sendResponse(__('messages.module_name_retrieved_successfully', ['moduleName' => __('labels.stack_module')]), $data, false);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.stack_module')]), ['general' => $e->getMessage()], 500, true);
        }
    }

    /**
     * @OA\Post(
     *       path="/upload-file",
     *       tags = {"Common"},
     *       summary = "Upload File",
     *       operationId = "Upload File",
     *       security={{"bearer_token":{}}, {"x_localization":{}}},
     *       @OA\RequestBody(
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                   @OA\Property(
     *                       property="upload_file",
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
    public function uploadFile(FileUploadRequest $request)
    {
        try {
            $fileName = FunctionUtils::uploadFileOnS3($request->upload_file, config('global.UPLOAD_PATHS.USER_PROFILE'));
            $data = FunctionUtils::getS3FileUrl($fileName, config('global.UPLOAD_PATHS.USER_PROFILE'));

            return $this->sendResponse(__('messages.file_uploaded_successfully'), $data, true);
        } catch (\Throwable $e) {
            return $this->sendError(__('messages.failed_to_upload_file'), ['general' => $e->getMessage(), $e->getFile()], 500, true);
        }
    }

    /**
     * @OA\Post(
     *       path="/delete-file",
     *       tags = {"Common"},
     *       summary = "Delete File",
     *       operationId = "Delete File",
     *       security={{"bearer_token":{}}, {"x_localization":{}}},
     *       @OA\RequestBody(
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                   @OA\Property(
     *                       property="file_url",
     *                       type="string",
     *                       description="Validations: url",
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
    public function deleteFile(DeleteFileRequest $request)
    {
        try {
            $s3Url = FunctionUtils::getS3Url();
            $file = str_replace($s3Url, "", $request->file_url);

            FunctionUtils::deleteFileOnS3($file);

            return $this->sendResponse(__('messages.file_deleted_successfully'), __('messages.file_deleted_successfully'), true);
        } catch (Exception $e) {
            return $this->sendError(__('messages.failed_to_delete_file'), ['general' => $e->getMessage()], 500, true);
        }
    }
}
