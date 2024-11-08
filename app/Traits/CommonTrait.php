<?php

namespace App\Traits;

use DB;
use Auth;
use Validator;
use Carbon\Carbon;
use App\Models\LovPrivileges;
use function Symfony\Component\Console\Input\isArray;

trait CommonTrait
{

    public static $siteName  = 'Admin';
    public static $siteEmail = 'info@admin.com';

    /* Users */
    public static $nameSize              = 100;
    public static $phoneSize             = 15;
    public static $phoneSizeMin          = 10;
    public static $addressSize           = 1000;
    public static $emailSize             = 100;
    public static $passwordMinSize       = 5;
    public static $passwordSize          = 20;
    public static $imageSize             = 5120;
    public static $fileSize              = 5120;
    public static $imageMimes            = "jpg,jpeg,png,gif";
    public static $imageAccept           = "image/jpg,image/jpeg,image/png,image/gif";
    public static $videoSize             = 5120;
    public static $videoMimes            = "flv,mp4,3gpp";
    public static $videoAccept           = "video/x-flv,video/mp4,video/3gpp";
    public static $doubleReg             = '/^\d*(?:\.\d+)?$/i';
    public static $phoneNumberExpression = '/^[0-9 +]+$/u';

    /**
     * Clean string or array.
     *
     * @param string|array
     * @return string|array
     */
    public function cleanString($badString)
    {
        if (is_array($badString)) {
            foreach ($badString as &$badStringO) {
                $badStringO = filter_var($badStringO, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
                $badStringO = trim(strip_tags(mb_convert_encoding(utf8_encode($badStringO), 'UTF-8', 'UTF-8')));
            }
        } elseif (!empty($badString)) {
            $badString = filter_var($badString, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
            $badString = trim(strip_tags(mb_convert_encoding(utf8_encode($badString), 'UTF-8', 'UTF-8')));
        }
        return $badString;
    }

    /**
     * Cleaning input value
     * @param input value
     * @return string|string[]
     */
    function cleanInput($input)
    {
        $search = array(
            '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
        );
        return preg_replace($search, '', $input);
    }

    function sqlPrevents($input)
    {
        if (is_array($input)) {
            foreach ($input as $var => $val) {
                $output[$var] = $this->sqlPrevents($val);
            }
        } else {
            if (get_magic_quotes_gpc()) {
                $input = stripslashes($input);
            }
            $input  = $this->cleanInput($input);
            $output = mysql_real_escape_string($input);
        }
        return $output;
    }

    /**
     * Make Sql Where Query From Filter data
     * @param $type
     * @param $key
     * @param $filterArray
     * @param $query
     * @return mixed $where return sql where condition
     */
    public function createWhere($type, $key, $filterArray, $query)
    {
        if (empty($filterArray)) {
            return $query;
        }
        $match = $filterArray['type'] ?? '';

        if (isset($filterArray['filter_type']) && $filterArray['filter_type'] == "date") {
            $filterArray['filter'] = 0;
        }

        if (isset($filterArray['filter_type']) && $filterArray['filter_type'] == "set") {
            $filterArray['filter'] = $filterArray['values'];
        }

        $value   = (!empty($filterArray['filter']) || $filterArray['filter'] == 0) ? $filterArray['filter'] : (is_array($filterArray['filter']) ? $filterArray['filter'] : "");
        $valueTo = $filterArray['filter_to'] ?? "";

        switch ($type) {
            case "text": //if filter type will be text so it goes here
                $value = strip_tags($value);
                $value = str_replace('`', '', $value);
                $value = str_replace("'", "", $value);
                $value = str_replace('%', '\%', $value);
                if ($match == "contains") {
                    $query = $query->where($key, 'LIKE', '%' . $value . '%');
                } else if ($match == "notContains") {
                    $query = $query->where($key, 'NOT LIKE', '%' . $value . '%');
                } else if ($match == "equals") {
                    $query = $query->where($key, '=', $value);
                } else if ($match == "notEqual") {
                    $query = $query->where($key, '!=', $value);
                } else if ($match == "startsWith") {
                    $query = $query->where($key, 'LIKE', $value . '%');
                } else if ($match == "endsWith") {
                    $query = $query->where($key, 'LIKE', '%' . $value);
                }
                break;
            case "number": //if filter type will be number so it goes here
                $value   = (!empty($filterArray['filter'])) ? $filterArray['filter'] : 0;
                $valueTo = (!empty($filterArray['filter_to'])) ? $filterArray['filter_to'] : 0;
                if ($match == "equals") {
                    $query = $query->where("$key", '=', $value);
                } else if ($match == "notEqual") {
                    $query = $query->where("$key", '!=', $value);
                } else if ($match == "lessThan") {
                    $query = $query->where("$key", '<', $value);
                } else if ($match == "lessThanOrEqual") {
                    $query = $query->where("$key", '<=', $value);
                } else if ($match == "greaterThan") {
                    $query = $query->where("$key", '>', $value);
                } else if ($match == "greaterThanOrEqual") {
                    $query = $query->where("$key", '>=', $value);
                } else if ($match == "inRange") {
                    $query = $query->whereBetween("$key", [$value, $valueTo]);
                }
                break;
            case "date":
                $dateFrom = date('Y-m-d', strtotime($filterArray['date_from']));
                $dateTo   = !empty($filterArray['date_to']) ? date('Y-m-d', strtotime($filterArray['date_to'])) : null;
                if ($match == "equals") {
                    $query = $query->where(\DB::raw("date_format($key, '%Y-%m-%d')"), '=', $dateFrom);
                } else if ($match == "notEqual") {
                    $query = $query->where(\DB::raw("date_format($key, '%Y-%m-%d')"), '!=', $dateFrom);
                } else if ($match == "lessThan") {
                    $query = $query->where(\DB::raw("date_format($key, '%Y-%m-%d')"), '<', $dateFrom);
                } else if ($match == "greaterThan") {
                    $query = $query->where(\DB::raw("date_format($key, '%Y-%m-%d')"), '>', $dateFrom);
                } else if ($match == "inRange") {
                    $query = $query->whereBetween(\DB::raw("date_format($key, '%Y-%m-%d')"), [$dateFrom, $dateTo]);
                }
                break;

            case "set":
                $query = $query->whereIn($key, $value);
                break;
        }
        return $query;
    }

    public static function base_url()
    {
        return url("/");
    }

    public static function getUploadPath()
    {
        $path = public_path() . '/uploads/';
        if (!is_dir($path)) {
            mkdir($path);       //create the directory
            chmod($path, 0777); //make it writable
        }
        return $path;
    }

    /* To get url of files */
    public static function getUploadUrl()
    {
        return url('uploads/');
    }

    public static function getDefaultUrl()
    {
        return url('uploads/default') . '/';
    }

    public static function getUserUrl()
    {
        return url('uploads/users') . '/';
    }

    public static function getImageSizes()
    {
        return [0 => ["width" => 100, "height" => 100], 1 => ["width" => 250, "height" => 250], 2 => ["width" => 500, "height" => 500], 3 => ['width' => 460, 'height' => 300]];
    }

    public static function getStatus()
    {
        return array(
            0 => "Inactive",
            1 => "Active",
        );
    }

    public static function getOtpForUser($length = 6)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    public static function timeElapsedString($datetime, $full = 0)
    {
        $now  = new \DateTime;
        $ago  = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {

            if ($diff->$k && $k != 's') {
                $v = $diff->$k . ' ' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!empty($full)) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    // utilities menu
    public function utilities_menu($userPrivileges)
    {
        if ($userPrivileges) {
            return LovPrivileges::select('id', 'group_id', 'name', 'controller')
                ->whereIn('id', $userPrivileges)
                ->where([
                    'parent_id' => 0,
                    'is_active' => 1,
                ])
                ->orderBy('name')
                ->get()->toArray();
        }
    }
}
