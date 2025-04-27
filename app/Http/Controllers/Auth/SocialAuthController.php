<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialAuthController extends Controller
{
    // Google Redirect
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->with(['prompt' => 'select_account']) // force account chooser
            ->redirect();
    }

    // Google Callback
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'profile_image' => $googleUser->getAvatar(),
                'password' => bcrypt(str()->random(16)), // random fallback password
            ]
        );

        $this->assignUserRole($user, 'user');

        $token = JWTAuth::fromUser($user);
        $user->load('roles');

        echo "<script>
            window.opener.postMessage(" . json_encode([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->profile_image,
                'roles' => $user->roles->pluck('name'),
            ],
            'token' => $token,
        ]) . ", '*');
            window.close();
        </script>";
        exit;
    }

    // GitHub Redirect
    public function redirectToGitHub()
    {
        return Socialite::driver('github')->stateless()->redirect();
    }

    // GitHub Callback
    public function handleGitHubCallback()
    {
        $githubUser = Socialite::driver('github')->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $githubUser->getEmail()],
            [
                'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                'email' => $githubUser->getEmail(),
                'profile_image' => $githubUser->getAvatar(),
                'password' => bcrypt(str()->random(16)),
            ]
        );

        $this->assignUserRole($user, 'user');

        $token = JWTAuth::fromUser($user);
        $user->load('roles');

        echo "<script>
            window.opener.postMessage(" . json_encode([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->profile_image,
                'roles' => $user->roles->pluck('name'),
            ],
            'token' => $token,
        ]) . ", '*');
            window.close();
        </script>";
        exit;
    }

    // 🔐 Assign role manually if not already
    protected function assignUserRole($user, $roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$user->roles->contains('name', $roleName)) {
            $user->roles()->attach($role->id);
        }
    }
}