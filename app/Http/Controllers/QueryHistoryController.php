<?php

namespace App\Http\Controllers;

use App\Models\QueryHistory;
use Illuminate\Http\Request;

class QueryHistoryController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            QueryHistory::where('client_id', $request->user()->id)
                ->when($request->type, fn($q, $t) => $q->where('type', $t))
                ->latest('executed_at')
                ->get()
        );
    }

    public function destroy(Request $request, $id)
    {
        QueryHistory::where('client_id', $request->user()->id)
            ->findOrFail($id)
            ->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function destroyAll(Request $request)
    {
        QueryHistory::where('client_id', $request->user()->id)
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->delete();

        return response()->json(['message' => 'History cleared']);
    }
}
