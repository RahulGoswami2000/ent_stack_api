<?php

namespace App\Repositories\Admin;

use App\Library\FunctionUtils;
use App\Traits\CommonTrait;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Models\Role;
use App\Models\LovPrivileges;
use App\Models\LovPrivilegeGroups;
use App\Library\Setting;

class UserRepository extends BaseRepository
{

    use CommonTrait;

    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Set Otp for user
     */
    public function setOtp($postData)
    {
        do {
            $otp      = CommonTrait::getOtpForUser(6);
            $otpFound = \DB::table('password_resets')->where([
                'token' => $otp,
            ])->count();
        } while ($otpFound != 0);

        \DB::table('password_resets')->where([
            'email' => $postData['email'],
        ])->delete();

        \DB::table('password_resets')->insert([
            'email'      => $postData['email'],
            'token'      => $otp,
            'created_at' => \Carbon\Carbon::now(),
        ]);
        $user          = User::select('first_name', 'last_name', 'email', 'country_code', 'mobile_no')->where('email', $postData['email'])->first();
        $templateData  = [
            'user'    => $user,
            'otpLink' => env('ADMIN_URL') . "reset-password/" . base64_encode($otp),
            'email'   => $user->email,
        ];

        dispatch(new \App\Jobs\SendTemplateEmailJob(config('global.MAIL_TEMPLATE.FORGOT_PASSWORD'), $templateData));
    }

    /**
     * Check Otp Exists
     */
    public function checkOtpExists($otp)
    {
        return \DB::table('password_resets')->where(
            'token',
            $otp
        )->first();
    }

    /**
     * Get User by email
     */
    public function getUserByEmail($email)
    {
        // var_dump($email); exit;
        return User::where('email', $email)->first();
    }

    /**
     * Get User by phone number
     */
    public function getUserByPhoneNumber($phoneNumber)
    {
        return User::where('mobile_no', $phoneNumber)->first();
    }

    /**
     * Set Password
     */
    public function setPassword($user, $password)
    {
        $user->update([
            'password' => bcrypt($password),
        ]);
        return $user;
    }

    /**
     * Get User permission by User Id
     */
    public function deleteOtp($otp)
    {
        \DB::table('password_resets')->where([
            'token' => $otp,
        ])->delete();
    }

    /**
     * Get User permission by User Id
     */
    public function getUserPermissionById($userId)
    {
        return \DB::table('user_role_matrix')
            ->leftJoin('role_permission_matrix', 'role_permission_matrix.role_id', '=', 'user_role_matrix.role_id')
            ->leftJoin('permission', 'permission.id', '=', 'role_permission_matrix.permission_id')
            ->where('user_id', $userId)
            ->whereNull('user_role_matrix.deleted_at')
            ->whereNull('role_permission_matrix.deleted_at')
            ->get(['permission.id', 'permission.permission_name'])
            ->toArray();
    }

    public function updateProfile($id, $request)
    {
        $data                = $this->user->findOrFail($id);
        $data->first_name    = $request->first_name;
        $data->last_name     = $request->last_name;
        $data->job_role      = $request->job_role;
        $data->date_of_birth = $request->date_of_birth;
        $data->start_date    = $request->start_date;
        $data->country_code  = $request->country_code;
        $data->mobile_no     = $request->mobile_no;

        $data->save();
        return $data;
    }

    public function details($id)
    {
        $menu = [];
        $me   = $this->user->find($id);
        $user = (object)$me;

        $rolePrivilegeData = $me->role()->select(['id', 'name', 'privileges'])->first();

        if (empty($user->privileges)) {
            $userPrivileges = array_unique(array_filter(explode('#', $rolePrivilegeData->privileges)));
        } else {
            $userPrivileges = array_unique(array_filter(explode('#', $user->privileges)));
        }

        $userPrivilegesKey = [];
        $temp              = [];
        if ($userPrivileges) {
            $lovPrivileges = LovPrivileges::select('id', 'parent_id', 'group_id', 'name as label', 'controller as key', 'permission_key')
                ->whereIn('id', $userPrivileges)
                ->where('is_active', 1)
                ->whereIn('menu_type', [config('global.MENU_TYPE.BOTH.id'), config('global.MENU_TYPE.ADMIN.id')])
                ->orderBy('sequence')
                ->get();

            foreach ($lovPrivileges as $privileges) {
                $userPrivilegesKey[] = $privileges->permission_key;
                if ($privileges->parent_id == 0) {
                    unset($privileges->permission_key);
                    $groupId = $privileges->group_id;
                    if (empty($groupId)) {
                        $menu[$privileges->id]['id']        = $privileges->id;
                        $menu[$privileges->id]['label']     = $privileges->label;
                        $menu[$privileges->id]['key']       = $privileges->key;
                        $menu[$privileges->id]['parent_id'] = $privileges->parent_id;
                        $menu[$privileges->id]['group_id']  = $privileges->group_id;
                    } else {
                        $group                        = $privileges->group()->first(['id', 'name']);
                        $menu[$groupId]['id']         = $privileges->id;
                        $menu[$groupId]['label']      = $group->name;
                        $menu[$groupId]['key']        = $privileges->id;
                        $menu[$groupId]['parent_id']  = $privileges->parent_id;
                        $menu[$groupId]['group_id']   = $privileges->group_id;
                        $menu[$groupId]['children'][] = $privileges;
                    }
                }
            }
            sort($userPrivilegesKey);
        }

        $user->temp           = $temp;
        $user->userPrivileges = $userPrivilegesKey;
        $user->role           = $rolePrivilegeData;
        $menu                 = array_values($menu);
        $user->menu           = collect($menu)->sortBy('name')->values();
        //        $user->utilities_menu = $this->utilities_menu($userPrivileges);
        return $user;
    }
}
