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
    public function handle(Request $request, Closure $next, ...$role_ids): Response
    {
        // Check if the user is authenticated and their role matches one of the allowed roles
        if (Auth::check() && in_array(Auth::user()->role_id, $role_ids)) {
            return $next($request);
        }

        // If the role doesn't match, return a 403 Unauthorized response
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
// if (Auth::check()) {
//     $roles = array_map('trim', explode(',', $roles));

//     Log::info('User Role ID: ' . Auth::user()->role_id);
//     Log::info('Allowed Roles: ' . implode(',', $roles));

//     if (in_array(Auth::user()->role_id, $roles)) {
//         return $next($request);
//     }
// }

// return redirect()->back();
