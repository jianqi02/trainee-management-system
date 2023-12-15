<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $lastLoginTime = $user->last_login;

            if ($lastLoginTime) {
                $lastLoginTime = Carbon::parse($lastLoginTime);
                $timeDifference = $lastLoginTime->diffInSeconds(Carbon::now());
                $sessionLifetime = env('CUSTOMIZED_LIFETIME'); // the lifetime in seconds

                if ( $timeDifference > $sessionLifetime) {
                    // Session has expired
                    $user->session_id = null;
                    $user->last_login = null;
                    $user->save();

                    Auth::logout();

                    return redirect('/login')->with('status', 'Your session has expired. Please log in again.');
                }
            }
        }

        return $next($request);
    }
}
