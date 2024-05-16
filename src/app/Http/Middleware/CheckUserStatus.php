<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check() && Auth::user()->status == User::BANNED) {
            $notify[] = ['error',translate("Your account is banned by admin")];
            return  (new AuthenticatedSessionController())->destroy()->withNotify($notify);
        }
        return $next($request);
    }
}
