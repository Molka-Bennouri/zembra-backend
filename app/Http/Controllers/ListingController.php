<?php

namespace App\Http\Controllers;

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

        return response()->json($response->json(), $response->status());
    }
}
