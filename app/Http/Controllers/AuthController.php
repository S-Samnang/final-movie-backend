<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user and assign "user" role.
     */
    public function store(Request $request) // used for register
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password)
        ]);

        if ($user->roles()->count() === 0) {
            $user->roles()->attach(2); // or get role by name 'user'
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Authenticate user and return token.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = JWTAuth::user();
        $user->load('roles'); // 💡 load roles and permissions

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'), // ✅ proper dynamic roles

            ]
        ]);
    }


    /**
     * Show a specific user's data.
     */
    public function show(string $id)
    {
        $user = User::with(['roles.permissions'])->find($id);

        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Extract permissions through roles
        $permissions = $user->roles
            ->flatMap(function ($role) {
                return $role->permissions;
            })
            ->pluck('name')
            ->unique()
            ->values();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $permissions,
            ]
        ]);
    }
}
