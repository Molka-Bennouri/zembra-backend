<?php

namespace App\Http\Controllers;

use App\Models\QueryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReviewController extends Controller
{
    public function create(Request $request)
    {
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . config('services.zembra.key'),
        ])->post('https://api.zembra.io/reviews', array_filter([
            'network'    => $request->query('network'),
            'slug'       => $request->query('slug'),
            'fields'     => $request->query('fields', []),
            'monitoring' => 'none',
        ]));

        $responseData = $response->json();

        if ($request->user('clients')) {
            QueryHistory::create([
                'client_id'   => $request->user('clients')->id,
                'type'        => 'reviews',
                'network'     => $request->query('network'),
                'slug'        => $request->query('slug'),
                'fields'      => $request->query('fields', []),
                'filters'     => null,
                'status'      => $response->successful() ? 'SUCCESS' : 'ERROR',
                'response'    => $responseData,
                'executed_at' => now(),
            ]);
        }

        return response()->json($responseData, $response->status());
    }

    public function fetch(Request $request)
    {
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . config('services.zembra.key'),
        ])->get('https://api.zembra.io/reviews', [
            'network' => $request->query('network'),
            'slug'    => $request->query('slug'),
        ]);

        return response()->json($response->json(), $response->status());
    }
}
