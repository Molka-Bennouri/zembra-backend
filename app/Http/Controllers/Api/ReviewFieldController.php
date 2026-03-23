<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReviewField;
use Illuminate\Http\JsonResponse;

class ReviewFieldController extends Controller
{
    public function index(): JsonResponse
    {
        $fields = ReviewField::orderBy('id')->get();

        return response()->json([
            'status' => 'success',
            'data' => $fields,
            'count' => $fields->count(),
        ]);
    }
    public function show(ReviewField $reviewField): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $reviewField,
        ]);
    }
}
