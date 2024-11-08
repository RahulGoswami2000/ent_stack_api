<?php

namespace App\Library;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class Setting
{

    public static $siteName  = 'Entrepreneur Stack';
    public static $siteEmail = 'info@EntrepreneurStack.com';

    /* Global validation */
    public static $nameMin             = 3;
    public static $nameMax             = 50;
    public static $emailMin            = 3;
    public static $emailMax            = 70;
    public static $passwordMin         = 8;
    public static $passwordMax         = 20;
    public static $passwordRegex       = "/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%@&!\"_',\}\{.\]\[<>?\~\`;:\/+\-\\*]).*$/";
    public static $imageSize           = 1024;
    public static $imageMimes          = "jpg,jpeg,png,gif,heif,heic";
    public static $imageAccept         = "image/jpg,image/jpeg,image/png,image/gif,image/heif,image/heic";
    public static $videoAcceptSize     = 5120;
    public static $videoMimes          = "flv,mp4,3gpp";
    public static $videoAcceptType     = "video/x-flv,video/mp4,video/3gpp";
    public static $doubleReg           = '/^\d*(?:\.\d+)?$/i';
    public static $mobMin              = 4;
    public static $mobMax              = 20;
    public static $mobRegex            = "/^[0-9]+$/";
    public static $projectNameMax      = 100;
    public static $descriptionMin      = 3;
    public static $descriptionMax      = 200;
    public static $referCode           = 8;
    public static $companyNameMax      = 100;
    public static $myTeamNameMin       = 2;
    public static $countryCodeMin      = 2;
    public static $countryCodeMax      = 10;
    public static $companyStackNameMax = 100;

    /**
     * success response method.
     *
     * @param      $message
     * @param      $result
     * @param bool $notify
     * @return JsonResponse
     */
    public static function sendResponse($message, $result, $notify = false)
    {
        $response = [
            'message' => $message,
            'data'    => $result,
        ];

        $response['notify'] = (!empty($notify)) ? $message : "";
        $response['errors'] = [];

        return response()->json($response, 200);
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
    public static function sendError($error, $errorMessages = [], $code = 404, $notify = false)
    {
        $response = [
            'message' => $error,
            'data'    => (object)[],
        ];

        $response['notify'] = (!empty($notify)) ? $error : "";
        $response['errors'] = $errorMessages;

        return response()->json($response, $code);
    }

    public static function getParsedReadableFormula(&$formula, $replaceArray = [], $replaceKey = null)
    {
        if (!empty($formula['operator'])) {
            if (!empty($formula['operand1']['value']) && isset($formula['operand1']['value']['unit']) && $formula['operand1']['value']['unit'] === 0 && $formula['operator'] === "-") {
                return "( " . $formula['operator'] . " " . self::getParsedReadableFormula($formula['operand2'], $replaceArray, $replaceKey) . " )";
            }
            return "( " . self::getParsedReadableFormula($formula['operand1'], $replaceArray, $replaceKey) . " " . $formula['operator'] . " " . self::getParsedReadableFormula($formula['operand2'], $replaceArray, $replaceKey) . " )";

        } else if (!empty($formula['value'])) {
            if ($formula['value']['type'] === "unit") {
                return $formula['value']['unit'];
            } else {
                if ($formula['value']['type'] === "item") {
                    $nodeDataKey = array_search($formula['value']['item']['value'], array_column($replaceArray, 'node_id'));
                    if ($nodeDataKey === false) {
                        $nodeDataKey = array_search($formula['value']['item']['value'], array_column($replaceArray, 'id'));
                    }
//                    return $nodeDataKey !== false && $replaceKey ? $replaceArray[$nodeDataKey][$replaceKey] : $formula['value']['item']['text'];

                    if ($nodeDataKey !== false && $replaceKey) {
                        $formula['value']['item']['text'] = $replaceArray[$nodeDataKey]['name'];
                        return $replaceArray[$nodeDataKey][$replaceKey];
                    } else {
                        return $formula['value']['item']['text'];
                    }
                }
            }
        }
        return "";
    }

    public static function getParsedArithmeticFormula($formula, $type = 'text')
    {
        if (!empty($formula)) {
            if (!empty($formula['operator'])) {
                return "(###" . self::getParsedArithmeticFormula($formula['operand1'], $type) . "###" . $formula['operator'] . "###" . self::getParsedArithmeticFormula($formula['operand2'], $type) . "###)";
            } else if (!empty($formula['value'])) {
                if (!empty($formula['value']['type']) && $formula['value']['type'] === "unit") {
                    return $formula['value']['unit'];
                } else {
                    if (!empty($formula['value']['type']) && $formula['value']['type'] === "item") {
                        if ($type == "both") {
                            return "|||" . $formula['value']['item']['value'] . ":" . $formula['value']['item']['text'] . "|||";
                        } else if ($type == "id") {
                            return "|||" . $formula['value']['item']['value'] . "|||";
                        } else {
                            return "|||" . $formula['value']['item']['text'] . "|||";
                        }
                    }
                }
            }
        }
        return null;

    }

    public static function calculateValue($formula)
    {
        try {
            return eval('return ' . $formula . ';');
        } catch (\DivisionByZeroError $e) {
            return 0;
        } catch (\ErrorException $e) {
            return 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
