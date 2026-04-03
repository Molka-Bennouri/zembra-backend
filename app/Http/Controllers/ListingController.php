<?php

namespace App\Http\Controllers;

use App\Models\QueryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ListingController extends Controller
{
    public function fetch(Request $request, string $network)
    {
        $slug   = $request->query('slug');
        $fields = $request->query('fields', '');

        $url = "https://api.zembra.io/listing/{$network}";

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . config('services.zembra.key'),
        ])->get($url, array_filter([
            'slug'   => $slug,
            'fields' => $fields ? explode(',', $fields) : null,
        ]));

        $responseData = $response->json();

        // Save to history if client is authenticated
        if ($request->user('clients')) {
            QueryHistory::create([
                'client_id'   => $request->user('clients')->id,
                'type'        => 'listing',
                'network'     => $network,
                'slug'        => $slug,
                'fields'      => $fields ? explode(',', $fields) : [],
                'filters'     => null,
                'status'      => $response->successful() ? 'SUCCESS' : 'ERROR',
                'response'    => $responseData,
                'executed_at' => now(),
            ]);
        }

        return response()->json($responseData, $response->status());
    }
}
