<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewDeviceLoginAlert;
use App\Models\Admin;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email_cell_username' => 'required',
            'password' => 'required',
        ]);

        $credentials = ['password' => $request->password];
        $input = $request->email_cell_username;

        $fields = ['email', 'cell', 'username'];
        foreach ($fields as $field) {
            $credentials[$field] = $input;

            if (Auth::guard('admin')->attempt($credentials)) {
                $user = Auth::guard('admin')->user();

                if (!$user->status) {
                    Auth::guard('admin')->logout();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Account is blocked. Contact the administrator.',
                    ], 403);
                }

                // IP Check & Alert
                $ip = $request->ip();
                if ($user->last_login_ip !== $ip) {
                    Mail::to($user->email)->send(new NewDeviceLoginAlert($user, $ip, now()));
                }

                $user->update(['last_login_ip' => $ip]);

                // Generate token
                $token = $user->createToken('admin-token')->plainTextToken;

                return response()->json([
                    'status' => 'success',
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => $user,
                ]);
            }

            unset($credentials[$field]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials',
        ], 401);
    }
}
