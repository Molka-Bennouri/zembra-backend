<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ClientController extends Controller
{
    // Register
    public function register(Request $request) {
        $request->validate([
            'full_name' => 'required|string',
            'email'     => 'required|email|unique:clients',
            'password'  => 'required|min:6',
        ]);

        $client = Client::create([
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($client);

        return response()->json(['token' => $token], 201);
    }

    // Login
    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('clients')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json(['token' => $token]);
    }

    // Get authenticated client
    public function me() {
        return response()->json(auth('clients')->user());
    }

    // Logout
    public function logout() {
        auth('clients')->logout();
        return response()->json(['message' => 'Logged out']);
    }
}
