<?php

namespace App\Http\Controllers;

use App\Models\QueryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MatchController extends Controller
{
    public function match(Request $request)
    {
        $name     = $request->query('name');
        $address  = $request->query('address');
        $lat      = $request->query('lat');
        $lng      = $request->query('lng');
        $networks = $request->query('networks', []);
        $fields   = $request->query('fields', []);

        $url = "https://localapi.zembra.io/listing/match";

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . config('services.zembra.key'),
        ])->withoutVerifying()->get($url, array_filter([
            'name'      => $name,
            'address'   => $address,
            'lat'       => $lat,
            'lng'       => $lng,
            'networks'  => $networks ?: null,
            'fields'    => $fields ?: null,
        ]));

        $responseData = $response->json();

        // Save to history if client is authenticated
        if ($request->user('api')) {
            QueryHistory::create([
                'user_id'     => $request->user()->id,
                'type'        => 'match',
                'network'     => implode(',', (array) $networks),
                'slug'        => null,  // pas de slug pour le match
                'fields'      => (array) $fields,
                'filters'     => array_filter([
                    'name'    => $name,
                    'address' => $address,
                    'lat'     => $lat,
                    'lng'     => $lng,
                ]),
                'status'      => $response->successful() ? 'SUCCESS' : 'ERROR',
                'response'    => $responseData,
                'executed_at' => now(),
            ]);
        }

        return response()->json($responseData, $response->status());
    }
}
