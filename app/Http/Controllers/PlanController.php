<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    /**
     * GET /api/plans
     */
    public function index(): JsonResponse
    {
        return response()->json(
            Plan::orderBy('amount')->get()
        );
    }

    /**
     * GET /api/plans/{id}
     */
    public function show(Plan $plan): JsonResponse
    {
        return response()->json($plan);
    }
}
