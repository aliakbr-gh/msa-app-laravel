<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getAllUsers(Request $request)
    {
        // $users = \DB::table('users')->get();
        $users = User::with('role')->latest()->paginate($this->perPage($request))->withQueryString();

        return response()->view('users.users', compact('users'));
    }

    public function editUser(Request $request, $id)
    {
        // $user = User::find($id);
        $user = \DB::table('users')->where('id', $id)->first();

        $roles = Role::latest()->get();

        if (! $user) {
            abort(404);
        }

        return response()->view('users.edit', compact('id', 'user', 'roles'));
    }

    public function updateUser(Request $request, $id)
    {
        $toLogout = ($request->user()->id === (int) $id) && ($request->password);

        $updatedUser = User::where('id', $id)->update([
            'username' => $request->username,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => (int) $request->role_id,
            'is_active' => (int) $request->is_active,
        ]);

        if ($toLogout) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return APIResponse::success('Password chanegd, Logging out...');
        }

        return APIResponse::success('User updated successfully', $updatedUser);
    }

    public function deleteUser(Request $request, $id)
    {
        $deletedUser = User::find($id);

        $response = $deletedUser->delete();

        return APIResponse::success('User deleted successfully', $response);
    }

    public function createUsersView()
    {
        $roles = Role::latest()->get();

        return response()->view('users.create', compact('roles'));
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:4',
            'role_id' => 'required',
        ]);

        $user = User::create([
            'username' => $request->username,
            'phone' => $request->phone,
            'role_id' => (int) $request->role_id,
            'password' => Hash::make($request->password),
            'is_active' => 1,
        ]);

        return APIResponse::success('User created successfully', $user, 201);
    }
}
