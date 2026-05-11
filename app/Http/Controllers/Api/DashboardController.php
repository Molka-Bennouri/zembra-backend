<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Network;
use Illuminate\Http\JsonResponse;
use App\Models\QueryHistory;

class DashboardController extends Controller
{
    public function kpis(): JsonResponse
    {
        $clientId = auth('api')->id();
        $since = now()->subDay();

        $requests24hQuery = QueryHistory::where('user_id', $clientId)
            ->where('executed_at', '>=', $since);

        $total   = (clone $requests24hQuery)->count();
        $success = (clone $requests24hQuery)->where('status', 'success')->count();
        $errors  = (clone $requests24hQuery)->where('status', 'error')->count();

        $successRate = $total > 0
            ? round(($success / $total) * 100, 2)
            : 0;

        $totalNetworks = Network::count();

        $networks = Network::select('id', 'name', 'label')
            ->get()
            ->map(fn($n) => [
                'id'     => $n->id,
                'name'   => $n->name,
                'label'  => $n->label,
                'active' => true,
            ]);

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

    public function requests(): JsonResponse
    {
        $clientId = auth('api')->id();

        $data = QueryHistory::where('user_id', $clientId)
            ->orderBy('executed_at', 'desc')
            ->take(5)
            ->get();

        return response()->json(
            $data->map(function ($r) {
                $response = $r->response ?? [];

                $code = null;
                if (isset($response['exception']['Code'])) {
                    $code = $response['exception']['Code'];
                } elseif (strtolower($r->status) === 'success') {
                    $code = 200;
                }

                return [
                    'id'          => $r->id,
                    'network'     => $r->network ?? 'unknown',
                    'slug'        => $r->slug ?? '-',
                    'status'      => strtolower($r->status ?? 'error'),
                    'status_code' => $code,
                    'created_at'  => $r->executed_at,
                ];
            })
        );
    }
}
