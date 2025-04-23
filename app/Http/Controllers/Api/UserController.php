<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Show authenticated user's profile with role & permissions
    public function me()
{
    $user = User::find(Auth::id()); // Retrieve the user as an Eloquent model

    $user->load('role', 'role.permissions');

    return response()->json([
        'user' => $user,
        'role' => $user->role->name,
        'permissions' => $user->role->permissions->pluck('name'),
    ]);
}

    // List all users with their roles
    public function index()
    {
        $users = User::with('role')->get();

        return response()->json($users);
    }

    // Assign or update a user's role
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = User::findOrFail($id);
        $user->role_id = $request->role_id;
        $user->save();

        return response()->json([
            'message' => 'Role updated successfully',
            'user' => $user->load('role')
        ]);
    }
}