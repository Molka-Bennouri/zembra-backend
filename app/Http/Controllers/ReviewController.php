<?php

namespace App\Http\Controllers;

use App\Models\QueryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReviewController extends Controller
{
    public function create(Request $request)
    {
        $params = $this->filterParams([
            'network'        => $request->query('network'),
            'slug'           => $request->query('slug'),
            'fields'         => $request->query('fields', []),
            'monitoring'     => 'none',
            'includeRawData' => $request->query('includeRawData'),
            'sortBy'         => $request->query('sortBy'),
            'sortDirection'  => $request->query('sortDirection'),
            'postedBefore'   => $request->query('postedBefore'),
            'postedAfter'    => $request->query('postedAfter'),
        ]);

        // Build the URL with query string manually so POST body stays clean
        $url = 'https://localapi.zembra.io/reviews?' . http_build_query($params);

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . config('services.zembra.key'),
        ])->withoutVerifying()->post($url);

        $responseData = $response->json();

        $postedAfter = $request->query('postedAfter');
        $postedBefore = $request->query('postedBefore');

        if ($postedAfter || $postedBefore) {
            $filtered = collect(data_get($responseData, 'data.reviews', []))
                ->filter(function ($review) use ($postedAfter, $postedBefore) {
                    $timestamp = strtotime($review['timestamp'] ?? '');
                    if (!$timestamp) return true;
                    if ($postedAfter && $timestamp < (int) $postedAfter) return false;
                    if ($postedBefore && $timestamp > (int) $postedBefore) return false;
                    return true;
                })
                ->values()
                ->toArray();

            data_set($responseData, 'data.reviews', $filtered);
            data_set($responseData, 'data.returned', count($filtered));
        }

        $sortBy = $request->query('sortBy');
        $sortDirection = $request->query('sortDirection', 'ASC');

        if ($sortBy) {
            $sorted = collect(data_get($responseData, 'data.reviews', []))
                ->sortBy(function ($review) use ($sortBy) {
                    return $review[$sortBy] ?? null;
                }, SORT_REGULAR, strtoupper($sortDirection) === 'DESC')
                ->values()
                ->toArray();

            data_set($responseData, 'data.reviews', $sorted);
        }

        $minRating = $request->query('minRating');
        $maxRating = $request->query('maxRating');

        if ($minRating !== null || $maxRating !== null) {
            $ratingFiltered = collect(data_get($responseData, 'data.reviews', []))
                ->filter(function ($review) use ($minRating, $maxRating) {
                    $rating = $review['rating'] ?? null;
                    if ($rating === null) return true;
                    if ($minRating !== null && $rating < (int) $minRating) return false;
                    if ($maxRating !== null && $rating > (int) $maxRating) return false;
                    return true;
                })
                ->values()
                ->toArray();

            data_set($responseData, 'data.reviews', $ratingFiltered);
            data_set($responseData, 'data.returned', count($ratingFiltered));
        }

        if ($request->user('api')) {
            QueryHistory::create([
                'user_id'   => $request->user()->id,
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
        ]));

        $responseData = $response->json();

        $postedAfter = $request->query('postedAfter');
        $postedBefore = $request->query('postedBefore');

        if ($postedAfter || $postedBefore) {
            $filtered = collect(data_get($responseData, 'data.reviews', []))
                ->filter(function ($review) use ($postedAfter, $postedBefore) {
                    $timestamp = strtotime($review['timestamp'] ?? '');
                    if (!$timestamp) return true;
                    if ($postedAfter && $timestamp < (int) $postedAfter) return false;
                    if ($postedBefore && $timestamp > (int) $postedBefore) return false;
                    return true;
                })
                ->values()
                ->toArray();

            data_set($responseData, 'data.reviews', $filtered);
            data_set($responseData, 'data.returned', count($filtered));
        }

        $sortBy = $request->query('sortBy');
        $sortDirection = $request->query('sortDirection', 'ASC');

        if ($sortBy) {
            $sorted = collect(data_get($responseData, 'data.reviews', []))
                ->sortBy(function ($review) use ($sortBy) {
                    return $review[$sortBy] ?? null;
                }, SORT_REGULAR, strtoupper($sortDirection) === 'DESC')
                ->values()
                ->toArray();

            data_set($responseData, 'data.reviews', $sorted);
        }

        $minRating = $request->query('minRating');
        $maxRating = $request->query('maxRating');

        if ($minRating !== null || $maxRating !== null) {
            $ratingFiltered = collect(data_get($responseData, 'data.reviews', []))
                ->filter(function ($review) use ($minRating, $maxRating) {
                    $rating = $review['rating'] ?? null;
                    if ($rating === null) return true;
                    if ($minRating !== null && $rating < (int) $minRating) return false;
                    if ($maxRating !== null && $rating > (int) $maxRating) return false;
                    return true;
                })
                ->values()
                ->toArray();

            data_set($responseData, 'data.reviews', $ratingFiltered);
            data_set($responseData, 'data.returned', count($ratingFiltered));
        }

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
        return array_filter($params, fn($v) => $v !== null && $v !== '' && $v !== []);
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
