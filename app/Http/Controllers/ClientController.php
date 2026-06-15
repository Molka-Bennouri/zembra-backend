<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use Stripe\Stripe;
use Stripe\Customer;

class ClientController extends Controller
{
    // ─────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────

    private function createStripeCustomer(Client $client): string
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $stripeCustomer = Customer::create([
            'email' => $client->email,
            'name'  => $client->full_name,
        ]);

        $client->update(['stripe_customer_id' => $stripeCustomer->id]);

        return $stripeCustomer->id;
    }

    private function syncStripeCustomer(Client $client): void
    {
        if (!$client->stripe_customer_id) return;

        Stripe::setApiKey(config('services.stripe.secret'));
        Customer::update($client->stripe_customer_id, [
            'email' => $client->email,
            'name'  => $client->full_name,
        ]);
    }

    // ─────────────────────────────────────────
    // AUTH
    // ─────────────────────────────────────────

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password'  => 'required|min:6',
        ]);

        $client = Client::create([
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        $token = auth('api')->login($client);

        return response()->json([
            'token'  => $token,
            'client' => [
                'id'        => $client->id,
                'full_name' => $client->full_name,
                'email'     => $client->email,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!$token = auth('api')->attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json(['token' => $token]);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Logged out']);
    }

    // ─────────────────────────────────────────
    // STRIPE
    // ─────────────────────────────────────────

    public function setupStripe()
    {
        $client = auth('api')->user();

        if ($client->stripe_customer_id) {
            return response()->json([
                'message'            => 'Stripe customer already exists.',
                'stripe_customer_id' => $client->stripe_customer_id,
            ], 200);
        }

        $stripeCustomerId = $this->createStripeCustomer($client);

        return response()->json([
            'message'            => 'Stripe customer created successfully.',
            'stripe_customer_id' => $stripeCustomerId,
        ], 201);
    }

    // ─────────────────────────────────────────
    // PROFILE
    // ─────────────────────────────────────────

    public function profile()
    {
        $client = auth('api')->user();

        return response()->json([
            'id'                 => $client->id,
            'full_name'          => $client->full_name,
            'email'              => $client->email,
            'stripe_customer_id' => $client->stripe_customer_id,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $client = auth('api')->user();

        $request->validate([
            'full_name' => 'required|string',
            'email'     => 'required|email|unique:users,email,' . $client->id,
        ]);

        $client->update([
            'full_name' => $request->full_name,
            'email'     => $request->email,
        ]);
        $this->syncStripeCustomer($client);

        return response()->json([
            'message' => 'Profile updated.',
            'user' => $client,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $client = auth('api')->user();

        if (!Hash::check($request->current_password, $client->password)) {
            return response()->json(['error' => 'Current password is incorrect.'], 403);
        }

        $client->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password updated.']);
    }

    public function deleteAccount(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $client = auth('api')->user();

        if (!Hash::check($request->password, $client->password)) {
            return response()->json(['error' => 'Incorrect password.'], 403);
        }

        if ($client->stripe_customer_id) {
            Stripe::setApiKey(config('services.stripe.secret'));
            Customer::retrieve($client->stripe_customer_id)->delete();
        }

        auth('api')->logout();
        $client->delete();

        return response()->json(['message' => 'Account deleted.']);
    }
}
