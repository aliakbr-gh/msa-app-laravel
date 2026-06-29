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
        $users = $this->applyDateRange(User::with('role')->latest(), $request)
            ->paginate($this->perPage($request))
            ->withQueryString();

        return response()->view('users.users', compact('users'));
    }

    public function editUser(Request $request, $id)
    {
        $user = User::find($id);
        $roles = Role::fixed();

        if (! $user) {
            abort(404);
        }

        return response()->view('users.edit', compact('id', 'user', 'roles'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|min:4',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
        ]);

        $toLogout = ($request->user()->id === (int) $id) && filled($data['password'] ?? null);
        if (filled($data['password'] ?? null)) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if ($toLogout) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return APIResponse::success('Password chanegd, Logging out...');
        }

        return APIResponse::success('User updated successfully', $user->fresh());
    }

    public function deleteUser(Request $request, $id)
    {
        abort_if((int) $request->user()->id === (int) $id, 422, 'You cannot delete your own user.');
        $deletedUser = User::findOrFail($id);

        $response = $deletedUser->delete();

        return APIResponse::success('User deleted successfully', $response);
    }

    public function createUsersView()
    {
        $roles = Role::fixed();

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
