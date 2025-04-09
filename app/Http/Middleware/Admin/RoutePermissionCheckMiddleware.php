<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoutePermissionCheckMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();

        // Check if logged in
        if (!$admin) {
            return redirect()
                ->route('admin.login.page')
                ->with('warning', 'You have to login to access this page');
        }

        $permissions = json_decode($admin->role->permission ?? '[]', true);

        // Use URI segment (e.g., 'admin-user' from /admin-user/edit/5)
        $uriSegment = $request->segment(1); // or segment(2) if your admin routes are prefixed like /admin/admin-user

        // Check permission
        if (in_array($uriSegment, $permissions)) {
            return $next($request);
        }

        return redirect()
            ->route('admin.dashboard.page')
            ->with('error', 'You do not have permission to access that page.');
    }
}
