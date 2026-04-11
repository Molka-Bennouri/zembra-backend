<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Network;
use Illuminate\Http\JsonResponse;
use App\Models\QueryHistory;

class DashboardController extends Controller
{
    /**
     * GET /api/kpis  — existant, inchangé
     */
    public function kpis(): JsonResponse
    {
        $since = now()->subDay();

        // Base query (QueryHistory)
        $requests24hQuery = QueryHistory::where('executed_at', '>=', $since);

        // Totals
        $total = (clone $requests24hQuery)->count();
        $success = (clone $requests24hQuery)->where('status', 'success')->count();
        $errors = (clone $requests24hQuery)->where('status', 'error')->count();

        $successRate = $total > 0
            ? round(($success / $total) * 100, 2)
            : 0;

        // Networks
        $totalNetworks = Network::count();

        $networks = Network::select('id', 'name', 'label')
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'name' => $n->name,
                    'label' => $n->label,
                    'active' => true, // fake (comme ton frontend l’attend)
                ];
            });



        return response()->json([
            'networks' => [
                'active' => $totalNetworks,
                'total'  => $totalNetworks,
                'list'   => $networks,
            ],
            'requests_24h' => [
                'total'   => $total,
                'success' => $success,
                'errors'  => $errors,
            ],
            'success_rate' => $successRate,
        ]);
    }

    /**
     * GET /api/dashboard/requests  — NOUVEAU
     * 50 dernières ScrapingRequests pour le tableau du dashboard.
     */
    public function requests()
    {
        $data = QueryHistory::orderBy('executed_at', 'desc')
            ->take(5)
            ->get();

        return response()->json(
            $data->map(function ($r) {
                $response = $r->response ?? [];

                // Extraire le code HTTP depuis exception.Code
                $code = null;
                if (isset($response['exception']['Code'])) {
                    $code = $response['exception']['Code'];
                } elseif (strtolower($r->status) === 'success') {
                    $code = 200;
                }

                return [
                    'id'         => $r->id,
                    'network'    => $r->network ?? 'unknown',
                    'slug'       => $r->slug ?? '-',
                    'status'     => strtolower($r->status ?? 'error'),
                    'status_code'=> $code,
                    'created_at' => $r->executed_at,
                ];
            })
        );
    }
}
