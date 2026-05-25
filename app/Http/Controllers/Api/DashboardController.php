<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Network;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\QueryHistory;
use Illuminate\Support\Facades\DB;

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

        // After
        $totalNetworks = Network::count();

        $usedNetworkNames = QueryHistory::where('user_id', $clientId)
            ->distinct()
            ->pluck('network');

        $networks = Network::select('id', 'name', 'label')
            ->get()
            ->map(fn($n) => [
                'id'     => $n->id,
                'name'   => $n->name,
                'label'  => $n->label,
                'active' => $usedNetworkNames->contains($n->name),
            ]);

        return response()->json([
            'networks' => [
                'active' => $usedNetworkNames->count(),
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

    public function chart(Request $request): JsonResponse
    {
        $clientId = auth('api')->id();
        $days     = (int) $request->query('days', 7);
        $days     = max(1, min($days, 90)); // clamp between 1 and 90

        $since = now()->subDays($days - 1)->startOfDay();

        // Aggregate by calendar date
        $rows = QueryHistory::where('user_id', $clientId)
            ->where('executed_at', '>=', $since)
            ->select([
                DB::raw('DATE(executed_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = \'success\' THEN 1 ELSE 0 END) as success_count'),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Build a complete range so missing days appear as zeros
        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date    = now()->subDays($i)->toDateString();
            $row     = $rows->get($date);
            $total   = $row ? (int) $row->total : 0;
            $success = $row ? (int) $row->success_count : 0;

            $result[] = [
                'label'        => now()->subDays($i)->format('D'), // Mon, Tue …
                'date'         => $date,
                'total'        => $total,
                'success'      => $success,
                'success_rate' => $total > 0
                    ? round(($success / $total) * 100, 1)
                    : 0,
            ];
        }

        return response()->json($result);
    }

}
