<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QueryHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $since = now()->subDay();

        $query = QueryHistory::where('executed_at', '>=', $since);

        $total = (clone $query)->count();

        $success = (clone $query)
            ->where('status', 'success')
            ->count();

        $errors = (clone $query)
            ->where('status', 'error')
            ->count();

        $successRate = $total > 0
            ? round(($success / $total) * 100, 1)
            : 0;

        $activeUsers = QueryHistory::where('executed_at', '>=', $since)
            ->distinct('user_id')
            ->count('user_id');

        return response()->json([
            'stats' => [
                'requests' => $total,
                'successRate' => $successRate,
                'users' => $activeUsers,
                'errors' => $errors,
            ]
        ]);
    }

    public function chart(Request $request): JsonResponse
    {
        $days = (int) $request->query('days', 7);
        $since = now()->subDays($days - 1)->startOfDay();

        $rows = QueryHistory::where('executed_at', '>=', $since)
            ->select([
                DB::raw('DATE(executed_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status='success' THEN 1 ELSE 0 END) as success_count"),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $result = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();

            $row = $rows->get($date);

            $total = $row ? (int) $row->total : 0;
            $success = $row ? (int) $row->success_count : 0;

            $result[] = [
                'label' => now()->subDays($i)->format('D'),
                'value' => $total,
                'success' => $total > 0
                    ? round(($success / $total) * 100, 1)
                    : 0
            ];
        }

        return response()->json($result);
    }
    public function errors(): JsonResponse
    {
        $rows = QueryHistory::where('status', 'error')
            ->select(['executed_at', 'network', 'type', 'response'])
            ->orderByDesc('executed_at')
            ->limit(50)
            ->get();

        $grouped = $rows->groupBy(fn($r) => $r->network . '|' . $r->type)
            ->map(function ($group) {
                $first = $group->first();

                // Try to extract a code from the response JSON, fallback to 500
                $responseData = is_string($first->response)
                    ? json_decode($first->response, true)
                    : $first->response;

                $code = $responseData['exception']['Code'] ?? 500;

                return [
                    'time'     => Carbon::parse($first->executed_at)->format('H:i'),
                    'code'     => (int) $code,
                    'endpoint' => $first->network . '/' . $first->type,
                    'count'    => $group->count(),
                ];
            })
            ->values();

        return response()->json($grouped);
    }

    public function networks(): JsonResponse
    {
        $stats = QueryHistory::select('network', DB::raw('count(*) as count'))
            ->groupBy('network')
            ->orderByDesc('count')
            ->get();

        return response()->json($stats);
    }


}
