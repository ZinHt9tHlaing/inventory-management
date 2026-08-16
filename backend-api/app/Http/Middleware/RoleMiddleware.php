<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // middleware(['role:admin,manager']) would check if the user has either 'admin' or 'editor' role
        // roles = ['admin', 'manager']
        // in_array(admin, ['admin', 'manager']) => true
        // in_array(user, ['admin', 'manager']) => false
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 403);
        }
        return $next($request);
    }
}