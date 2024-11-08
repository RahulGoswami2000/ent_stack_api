<?php

namespace App\Http\Middleware;

use App\Library\Setting;
use Closure;
use Illuminate\Http\Request;

class CheckUserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param array                    $permissions
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $loggedInUser = $request->user();
        // Check user is logged in
        if (empty($loggedInUser)) {
            if (empty($request->bearerToken())) {
                $message = __('messages.you_are_not_logged_in');
            } else {
                $message = __('messages.your_session_has_been_expired_kindly_login_again');
            }
            return Setting::sendError($message, ['general' => $message], 401, true);
        }

        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        $user              = \DB::table('mst_users')->find($loggedInUser->id);
        $rolePrivilegeData = \DB::table('mst_roles')->whereNull('deleted_at')->where('id', $user->role_id)->first(['id', 'name', 'privileges']);

        if (empty($user->privileges)) {
            $userPrivileges = array_unique(array_filter(explode('#', $rolePrivilegeData->privileges)));
        } else {
            $userPrivileges = array_unique(array_filter(explode('#', $user->privileges)));
        }

        $privilegeList = [];
        if ($userPrivileges) {
            $privilegeList = \DB::table('lov_privileges')->select('id', 'group_id', 'name', 'controller', 'permission_key')
                ->whereIn('id', $userPrivileges)
                ->where([
                    'is_active' => 1
                ])
                ->get()->pluck('permission_key')->toArray();
        }

        $hasPermission = array_intersect($permissions, $privilegeList);

        // Match user roles to requested roles
        if (empty($privilegeList) || empty($hasPermission)) {
            $message = __('messages.you_do_not_have_the_permission_to_use_this_resource');
            return Setting::sendError($message, ['general' => $message], 401, true);
        }

        return $next($request);
    }
}
