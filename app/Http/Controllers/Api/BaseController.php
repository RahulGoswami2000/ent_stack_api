<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as Controller;
use App\Library\Setting;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @param      $message
     * @param      $result
     * @param bool $notify
     * @return JsonResponse
     */
    public function sendResponse($message, $result, $notify = false)
    {
        return Setting::sendResponse($message, $result, $notify);
    }

    /**
     * return error response.
     *
     * @param       $error
     * @param array $errorMessages
     * @param int   $code
     * @param bool  $notify
     * @return JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404, $notify = false)
    {
        return Setting::sendError($error, $errorMessages, $code, $notify);
    }
}
