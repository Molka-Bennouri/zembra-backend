<?php

namespace App\Http\Controllers\Api;

use App\Models\ResponseField;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ResponseFieldController extends Controller
{
    /**
     * Get all response fields
     */
    public function index(): JsonResponse
    {
        $fields = ResponseField::orderBy('order')->get();

        return response()->json([
            'status' => 'success',
            'data' => $fields,
            'count' => $fields->count(),
        ]);
    }

    /**
     * Get only active response fields
     */
    public function getActive(): JsonResponse
    {
        $fields = ResponseField::getActive();

        return response()->json([
            'status' => 'success',
            'data' => $fields,
            'count' => $fields->count(),
        ]);
    }

    /**
     * Get fields grouped by type
     */
    public function grouped(): JsonResponse
    {
        $fields = ResponseField::getGroupedByType();

        return response()->json([
            'status' => 'success',
            'data' => $fields,
        ]);
    }

    /**
     * Create a new response field
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:response_fields',
            'label' => 'nullable|string',
            'description' => 'nullable|string',
            'field_type' => 'required|in:string,number,boolean,array,object',
            'is_active' => 'boolean',
            'is_required' => 'boolean',
            'order' => 'integer|min:0',
            'metadata' => 'nullable|array',
        ]);

        $field = ResponseField::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Field created successfully',
            'data' => $field,
        ], 201);
    }

    /**
     * Get a specific response field
     */
    public function show(ResponseField $responseField): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $responseField,
        ]);
    }

    /**
     * Update a response field
     */
    public function update(Request $request, ResponseField $responseField): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|unique:response_fields,name,' . $responseField->id,
            'label' => 'nullable|string',
            'description' => 'nullable|string',
            'field_type' => 'in:string,number,boolean,array,object',
            'is_active' => 'boolean',
            'is_required' => 'boolean',
            'order' => 'integer|min:0',
            'metadata' => 'nullable|array',
        ]);

        $responseField->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Field updated successfully',
            'data' => $responseField,
        ]);
    }

    /**
     * Delete a response field
     */
    public function destroy(ResponseField $responseField): JsonResponse
    {
        $responseField->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Field deleted successfully',
        ]);
    }

    /**
     * Bulk update field status (activate/deactivate)
     */
    public function toggleStatus(Request $request, ResponseField $responseField): JsonResponse
    {
        $responseField->update(['is_active' => !$responseField->is_active]);

        return response()->json([
            'status' => 'success',
            'message' => 'Field status updated',
            'data' => $responseField,
        ]);
    }

    /**
     * Reorder fields
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|integer|exists:response_fields',
            'fields.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['fields'] as $item) {
            ResponseField::findOrFail($item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Fields reordered successfully',
        ]);
    }
}
