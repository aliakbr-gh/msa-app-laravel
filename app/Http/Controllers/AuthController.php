<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
            'is_active' => 1,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return APIResponse::success('Login successfully', Auth::user());
        }

        return APIResponse::error('Invalid credentials or inactive user', [], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return APIResponse::success('Logged out successfully');
    }

    public function profile(Request $request)
    {
        $profile = $request->user()->only([
            'id',
            'username',
            'phone',
            'role',
            'is_active',
            'created_at',
        ]);

        $user = User::with('roles')->where('id', Auth::user()->id)->first();

        return response()->view('profile.profile', compact('profile', 'user'));
    }
}
