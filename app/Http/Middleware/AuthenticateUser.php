<?php

namespace App\Http\Middleware;

use App\Library\Setting;
use Closure;

class AuthenticateUser
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$userType)
    {
        if (empty(\Auth::user()) || !in_array(\Auth::user()->user_type, $userType)) {
            if (empty($request->bearerToken())) {
                $message = __('messages.you_are_not_logged_in');
            } else {
                $message = __('messages.your_session_has_been_expired_kindly_login_again');
            }
            return Setting::sendError($message, ['general' => $message], 401);
        }
        return $next($request);
    }
}
