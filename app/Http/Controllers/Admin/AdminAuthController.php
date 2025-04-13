<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\NewDeviceLoginAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminAuthController extends Controller
{
    public function showLoginPage()
    {
        return view('admin.pages.login');
    }

    public function Login(Request $request)
    {
        $this->validate($request, [
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

                // Check if the account is active
                if (!$user->status) {
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login.page')
                        ->with('warning', 'Your account is blocked. Please contact with Admin');
                }

                // IP Check & Send Alert
                $currentIp = $request->ip();
                if ($user->last_login_ip !== $currentIp) {
                    Mail::to($user->email)->send(new NewDeviceLoginAlert($user, $currentIp, now()->toDateTimeString()));
                }

                // Update last IP
                $user->update(['last_login_ip' => $currentIp]);

                return redirect()->route('admin.dashboard.page');
            }
            unset($credentials[$field]);
        }

        return redirect()->route('admin.login.page')
            ->with('warning', 'Email or Password incorrect');
    }
    public function Logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login.page')->with('success', 'Logout Successfully');
    }
}
