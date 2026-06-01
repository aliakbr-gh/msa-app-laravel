<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\APIResponse;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SetupController extends Controller
{
    public function setupProject(Request $request)
    {
        return response()->view('setup.setup');
    }

    public function addSU(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            's_key' => 'required',
        ]);

        $correct_s_key = "HnPmS12345";

        if($request->s_key !== $correct_s_key){
            return APIResponse::error("Setup Secret Key is incorrect");
        }

        $existingSU = User::where('username', 'superadmin')->first();

        if ($existingSU) {
            return APIResponse::error(
                "Project is already setup"
            );
        }

        $roles = [
            'superadmin',
            'admin',
            'reception',
            'pharmacy',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        User::create([
            'username' => 'superadmin',
            'phone' => null,
            'role_id' => 1,
            'password' => Hash::make($request->password),
            'is_active' => 1,
        ]);

        return APIResponse::success("Project setup completed successfully");
    }
}
