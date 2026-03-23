<?php

namespace App\Http\Controllers;

use App\Models\Network;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    // Create a network
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:networks,name',
            'slug_pattern' => 'required|string',
        ]);

        $network = Network::create([
            'name' => $request->name,
            'slug_pattern' => $request->slug_pattern,
        ]);

        return response()->json([
            'message' => 'Network created successfully',
            'network' => $network,
        ], 201);
    }

    // Get all networks
    public function index()
    {
        $networks = Network::all();

        return response()->json([
            'networks' => $networks,
        ], 200);
    }

    // Delete a network
    public function destroy($id)
    {
        $network = Network::find($id);

        if (!$network) {
            return response()->json(['message' => 'Network not found'], 404);
        }

        $network->delete();

        return response()->json(['message' => 'Network deleted successfully'], 200);
    }
}
