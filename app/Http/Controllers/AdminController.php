<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminController extends Controller
{
    public function listClients() {
        $clients = User::where('role', 'client')->with('clientProfile')->get();
        return response()->json($clients);
    }

    public function deleteClient($id) {
        $client = User::where('role', 'client')->findOrFail($id);
        $client->delete();
        return response()->json(['message' => 'Client deleted.']);
    }
}
