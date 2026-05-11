<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'full_name' => 'required|string',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:6',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'client', // toujours client à l'inscription
        ]);

        // Créer le profil client vide
        $user->clientProfile()->create([]);

        $token = auth('api')->login($user);

        return response()->json(['token' => $token], 201);
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = auth('api')->user();

        return response()->json([
            'token' => $token,
            'role'  => $user->role,  // le frontend sait où rediriger
        ]);
    }

    public function me() {
        return response()->json(auth('api')->user());
    }

    public function logout() {
        auth('api')->logout();
        return response()->json(['message' => 'Logged out']);
    }
}
