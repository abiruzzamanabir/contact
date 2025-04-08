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

        if (!$admin) {
            return redirect()
                ->route('admin.login.page')
                ->with('warning', 'You have to login to access this page');
        }

        $permissions = json_decode($admin->role->permission ?? '[]', true);
        $routeName = $request->route()->getName(); // e.g., "contact.index", "contact-type.index"

        foreach ($permissions as $permission) {
            if (str_starts_with($routeName, $permission)) {
                return $next($request);
            }
        }

        return redirect()
            ->route('admin.dashboard.page')
            ->with('error', 'You do not have permission to access that page.');
    }
}
