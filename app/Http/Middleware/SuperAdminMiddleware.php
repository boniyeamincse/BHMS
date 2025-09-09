<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has Super Admin role
        $isSuperAdmin = $user->roles()->where('name', 'Super Admin')->exists();

        if (!$isSuperAdmin) {
            return redirect()->route('hospital.dashboard')
                ->with('error', 'Access denied. Super Admin privileges required.');
        }

        // Add flag to request for scopes to check
        $request->merge(['is_super_admin' => true]);

        return $next($request);
    }
}