<?php

namespace App\Http\Middleware;

use App\Library\Setting;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (empty($request->bearerToken())) {
            $message = __('messages.you_are_not_logged_in');
        } else {
            $message = __('messages.your_session_has_been_expired_kindly_login_again');
        }
        return Setting::sendError($message, ['general' => $message], 401);
    }
}
