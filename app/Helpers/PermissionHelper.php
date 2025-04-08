<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('hasPermission')) {
    function hasPermission($key)
    {
        $user = Auth::guard('admin')->user();

        if (!$user || !$user->role) {
            return false;
        }

        $permissions = json_decode($user->role->permission ?? '[]', true);

        return in_array($key, $permissions);
    }
}
