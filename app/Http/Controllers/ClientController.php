<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function updateProfile(Request $request) {
        $user = auth('api')->user();

        $request->validate([
            'full_name' => 'required|string',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'phone'     => 'nullable|string',
            'address'   => 'nullable|string',
        ]);

        $user->update([
            'full_name' => $request->full_name,
            'email'     => $request->email,
        ]);

        $user->clientProfile()->update([
            'phone'   => $request->phone,
            'address' => $request->address,
        ]);

        return response()->json(['message' => 'Profile updated.', 'user' => $user->load('clientProfile')]);
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $user = auth('api')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect.'], 403);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password updated.']);
    }

    public function deleteAccount(Request $request) {
        $request->validate(['password' => 'required|string']);

        $user = auth('api')->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Incorrect password.'], 403);
        }

        auth('api')->logout();
        $user->delete();

        return response()->json(['message' => 'Account deleted.']);
    }
}
