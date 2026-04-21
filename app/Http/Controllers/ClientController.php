<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Stripe\Stripe;
use Stripe\Customer;

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
        // 2. create Stripe customer
        Stripe::setApiKey(config('services.stripe.secret'));

        $stripeCustomer = Customer::create([
            'email' => $client->email,
            'name'  => $client->full_name,
        ]);

        // 3. save stripe_customer_id in DB
        $client->update([
            'stripe_customer_id' => $stripeCustomer->id,
        ]);

        $token = auth('clients')->login($client);

        return response()->json([
            'token' => $token,
            'stripe_customer_id' => $stripeCustomer->id
        ], 201);
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

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $client = auth('clients')->user();

        // Check password
        if (!Hash::check($request->password, $client->password)) {
            return response()->json(['error' => 'Incorrect password.'], 403);
        }

        // Invalidate the JWT token
        auth('clients')->logout();

        // Delete the account
        $client->delete();

        return response()->json(['message' => 'Account deleted successfully.'], 200);
    }

    // Update profile (name & email)
    public function updateProfile(Request $request)
    {
        $client = auth('clients')->user();

        $request->validate([
            'full_name' => 'required|string',
            'email'     => 'required|email|unique:clients,email,' . $client->id,
        ]);

        $client->update([
            'full_name' => $request->full_name,
            'email'     => $request->email,
        ]);

        return response()->json(['message' => 'Profile updated successfully.', 'client' => $client]);
    }

// Update password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $client = auth('clients')->user();

        if (!Hash::check($request->current_password, $client->password)) {
            return response()->json(['error' => 'Current password is incorrect.'], 403);
        }

        $client->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password updated successfully.']);
    }
    public function profile()
    {
        $client = auth('clients')->user();

        return response()->json([
            'id' => $client->id,
            'full_name' => $client->full_name,
            'email' => $client->email,
            'stripe_customer_id' => $client->stripe_customer_id,
        ]);
    }
}
