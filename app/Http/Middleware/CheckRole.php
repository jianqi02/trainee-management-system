<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
{
    // Check if the user has one of the specified roles
    if ($request->user() && in_array($request->user()->role_id, $roles)) {
        return $next($request);
    }

    // Redirect or respond with an unauthorized message based on user role
    switch (Auth::user()->role_id ?? null) {
        case 1:
            return redirect('/admin-dashboard')->with('error', 'Unauthorized access.');
        case 2:
            return redirect('/sv-homepage')->with('error', 'Unauthorized access.');
        case 3:
            return redirect('/homepage')->with('error', 'Unauthorized access.');
        default:
            return redirect('/')->with('error', 'You are not logged in!');
    }
}
}
