<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Notifications\ResetClientPasswordNotification;

class PasswordController extends Controller
{
    /**
     * Envoyer le lien de réinitialisation au client
     */
    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:clients,email',
        ]);

        // Utiliser le broker "clients"
        $status = Password::broker('clients')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Reset link sent to your email.'
            ]);
        } else {
            return response()->json([
                'message' => 'Unable to send reset link.'
            ], 400);
        }
    }

    /**
     * Réinitialiser le mot de passe du client
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:clients,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::broker('clients')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Client $client, $password) {
                $client->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password has been reset successfully.'
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid token or email.'
            ], 400);
        }
    }
}
