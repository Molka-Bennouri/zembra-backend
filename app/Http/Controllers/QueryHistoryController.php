<?php

namespace App\Http\Controllers;

use App\Models\QueryHistory;
use Illuminate\Http\Request;

class QueryHistoryController extends Controller
{
    public function index(Request $request)
    {
        $client = auth('clients')->user();

        return response()->json(
            QueryHistory::where('client_id', $client->id)
                ->when($request->type, fn($q, $t) => $q->where('type', $t))
                ->latest('executed_at')
                ->get()
        );
    }

    public function destroy(Request $request, $id)
    {
        $client = auth('clients')->user();

        QueryHistory::where('client_id', $client->id)
            ->findOrFail($id)
            ->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function destroyAll(Request $request)
    {
        $client = auth('clients')->user();

        QueryHistory::where('client_id', $client->id)
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->delete();

        return response()->json(['message' => 'History cleared']);
    }
}
