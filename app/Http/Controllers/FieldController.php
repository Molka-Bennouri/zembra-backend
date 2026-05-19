<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FieldController extends Controller
{
    // GET /api/fields?context=review|listing
    public function index(Request $request): JsonResponse
    {
        $query = Field::orderBy('id');

        if ($request->has('context')) {
            $query->where('context', $request->context);
        }

        $fields = $query->get();

        return response()->json([
            'status' => 'success',
            'data'   => $fields,
            'count'  => $fields->count(),
        ]);
    }

    // GET /api/fields/{field}
    public function show(Field $field): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $field,
        ]);
    }

    // POST /api/fields
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'        => 'required|string|unique:fields,name',
            'label'       => 'nullable|string',
            'description' => 'nullable|string',
            'context'     => 'required|in:review,listing',
        ]);

        $field = Field::create($request->only(['name', 'label', 'description', 'context']));

        return response()->json([
            'status'  => 'success',
            'message' => 'Field created successfully',
            'data'    => $field,
        ], 201);
    }

    // PUT|PATCH /api/fields/{field}
    public function update(Request $request, Field $field): JsonResponse
    {
        $request->validate([
            'name'        => 'sometimes|string|unique:fields,name,' . $field->id,
            'label'       => 'sometimes|nullable|string',
            'description' => 'sometimes|nullable|string',
            'context'     => 'sometimes|in:review,listing',
        ]);

        $field->update($request->only(['name', 'label', 'description', 'context']));

        return response()->json([
            'status'  => 'success',
            'message' => 'Field updated successfully',
            'data'    => $field->fresh(),
        ]);
    }

    // DELETE /api/fields/{field}
    public function destroy(Field $field): JsonResponse
    {
        $field->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Field deleted successfully',
        ]);
    }
}
