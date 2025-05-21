<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CheckAuthRole
 *
 * Middleware to check if the authenticated user has the required role(s).
 */
class CheckAuthRole
{
    /**
     * Handle an incoming request and check user role.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string  ...$roles
     * @return Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $userRole = Auth::user()->role;
        // Combine roles from variadic parameter, splitting comma-separated roles
        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles = array_merge($allowedRoles, explode(',', $role));
        }

        // Check if user role is allowed
        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}