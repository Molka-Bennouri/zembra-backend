<?php

namespace App\Http\Controllers;

use App\Models\ScrapingHistory;
use Illuminate\Http\Request;

class ScrapingHistoryController extends Controller
{
    public function index()
    {
        return response()->json(
            ScrapingHistory::latest('executed_at')->get()
        );
    }

    public function destroy($id)
    {
        ScrapingHistory::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function destroyAll()
    {
        ScrapingHistory::truncate();
        return response()->json(['message' => 'History cleared']);
    }
}
