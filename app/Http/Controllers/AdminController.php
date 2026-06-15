<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function listClients() {
        $clients = User::where('role', 'client')->get();
        return response()->json($clients);
    }

    public function updateClient(Request $request, $id)
    {
        $client = User::where('role', 'client')->findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',   // ← not 'name'
            'email'    => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $client->update($validated);

        return response()->json($client);
    }

    public function deleteClient($id) {
        $client = User::where('role', 'client')->findOrFail($id);
        $client->delete();
        return response()->json(['message' => 'Client deleted.']);
    }
}
