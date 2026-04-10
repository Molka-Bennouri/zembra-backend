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
        ])->withoutVerifying()->withOptions([
            'query' => $this->filterParams([
                'network'    => $request->query('network'),
                'slug'       => $request->query('slug'),
                'fields'     => $request->query('fields', []),
                'monitoring' => 'none',
                'includeRawData' => $request->query('includeRawData'),
                'sortBy'        => $request->query('sortBy'),
                'sortDirection' => $request->query('sortDirection'),
                'postedBefore'  => $request->query('postedBefore'),
                'postedAfter'   => $request->query('postedAfter'),
            ])
        ])->post('https://localapi.zembra.io/reviews');

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
        ])->withoutVerifying()->get('https://localapi.zembra.io/reviews', $this->filterParams([
            'network' => $request->query('network'),
            'slug'    => $request->query('slug'),
            'fields'  => $request->query('fields', []),
            'includeRawData' => $request->query('includeRawData'),
            'sortBy'        => $request->query('sortBy'),
            'sortDirection' => $request->query('sortDirection'),
            'postedBefore'  => $request->query('postedBefore'),
            'postedAfter'   => $request->query('postedAfter'),
            // filters will plug in here once wired up:

            // 'limit'      => $request->query('limit'),
            // 'offset'     => $request->query('offset'),
            // 'min_rating' => $request->query('min_rating'),
            // 'max_rating' => $request->query('max_rating'),
        ]));

        $responseData = $response->json();

        $reviews = collect(data_get($responseData, 'data.reviews', []))
            ->pluck('text')
            ->filter()
            ->values()
            ->toArray();

        return response()->json([
            'zembra'  => $responseData,
            'reviews' => $reviews,
        ], $response->status());
    }

    private function filterParams(array $params): array
    {
        return array_filter($params, fn($v) => $v !== null && $v !== '');
    }

    public function analyze(Request $request)
    {
        set_time_limit(0); // Remove PHP time limit entirely

        $reviews = $request->input('reviews', []);

        if (empty($reviews)) {
            return response()->json(['message' => 'No reviews provided.'], 404);
        }

        $aiResponse = Http::withOptions([
            'connect_timeout' => 5,
            'timeout'         => 0, // No timeout — wait as long as needed
        ])->post('http://127.0.0.1:8001/analyze', [
            'reviews' => $reviews,
        ]);

        return response()->json($aiResponse->json(), $aiResponse->status());
    }
}
