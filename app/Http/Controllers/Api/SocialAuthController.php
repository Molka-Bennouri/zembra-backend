<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class SocialAuthController extends Controller
{
    // Google
    public function googleRedirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            return $this->handleSocialLogin($googleUser, 'google');
        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/login?error=google_auth_failed');
        }
    }

    // GitHub
    public function githubRedirect()
    {
        return Socialite::driver('github')->stateless()->redirect();
    }

    public function githubCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->stateless()->user();
            return $this->handleSocialLogin($githubUser, 'github');
        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/login?error=github_auth_failed');
        }
    }

    // Création / récupération utilisateur + JWT
    private function handleSocialLogin($socialUser, $provider)
    {
        // Cherche dans users au lieu de clients
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            // Nouveau user → toujours role client
            $user = User::create([
                'full_name'   => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email'       => $socialUser->getEmail(),
                'password'    => bcrypt(Str::random(24)),
                'role'        => 'client',
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
            ]);


        } else {
            $user->update([
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        // Génération du token JWT avec guard api
        $token = JWTAuth::fromUser($user);

        return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/callback?token=' . $token);
    }
}
