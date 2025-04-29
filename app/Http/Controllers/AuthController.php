<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpVerificationMail;

use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


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
            'password' => 'required|string|min:6|max:50',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password)
        ]);

        if ($user->roles()->count() === 0) {
            $user->roles()->attach(2); // or get role by name 'user'
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Save OTP to user
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // Send OTP email
        Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($user));

        return response()->json([
            'message' => 'OTP sent to email. Please verify to activate your account.',
        ]);
    }

    /**
     * Authenticate user and return token.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Check if user exists
        $user = User::where('email', $credentials['email'])->first();

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = JWTAuth::user();
        if (! $user->is_verified) {
            return response()->json(['error' => 'Email not verified.'], 403);
        }

        // Check if password is correct
        if (! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials.'], 401);
        }
        $user->load('roles'); // 💡 load roles and permissions

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'), // ✅ proper dynamic roles
                'profile_image' => $user->profile_image ?? null,

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

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || $user->otp !== $request->otp || now()->gt($user->otp_expires_at)) {
            return response()->json(['error' => 'Invalid or expired OTP'], 422);
        }

        $user->is_verified = true;
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json([
            'message' => 'Email verified successfully! Please log in.',
        ]);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['error' => 'User is already verified.'], 400);
        }

        $user->otp = rand(100000, 999999);
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new OtpVerificationMail($user));

        return response()->json(['message' => 'New OTP sent to your email.']);
    }

    public function requestPasswordReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'Email not found'], 404);
        }

        $token = Str::random(60);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => now()
            ]
        );

        Mail::to($request->email)->send(new PasswordResetMail($token));

        return response()->json(['message' => 'Password reset link sent to email']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:4|confirmed',
        ]);

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return response()->json(['error' => 'Invalid or expired token'], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete used token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password reset successful']);
    }
}