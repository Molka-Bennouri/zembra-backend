<?php

namespace App\Services;

use App\Models\ResponseField;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ResponseFieldService
{
    /**
     * Get all fields with caching
     */
    public function getAllFields(): EloquentCollection
    {
        return ResponseField::orderBy('order')->get();
    }

    /**
     * Get only active fields
     */
    public function getActiveFields(): EloquentCollection
    {
        return ResponseField::getActive();
    }

    /**
     * Get fields grouped by type
     */
    public function getGroupedFields(): Collection
    {
        return ResponseField::getGroupedByType();
    }

    /**
     * Get field names as array
     */
    public function getFieldNames(): array
    {
        return $this->getActiveFields()
            ->pluck('name')
            ->toArray();
    }

    /**
     * Get field labels as key-value array
     */
    public function getFieldLabels(): array
    {
        return $this->getActiveFields()
            ->pluck('label', 'name')
            ->toArray();
    }

    /**
     * Check if field exists and is active
     */
    public function isFieldActive(string $fieldName): bool
    {
        return ResponseField::where('name', $fieldName)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get field by name
     */
    public function getFieldByName(string $fieldName): ?ResponseField
    {
        return ResponseField::where('name', $fieldName)->first();
    }

    /**
     * Get required fields only
     */
    public function getRequiredFields(): EloquentCollection
    {
        return ResponseField::where('is_required', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get fields by type
     */
    public function getFieldsByType(string $type): EloquentCollection
    {
        return ResponseField::where('field_type', $type)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Create field with validation
     */
    public function createField(array $data): ResponseField
    {
        return ResponseField::create($data);
    }

    /**
     * Update field
     */
    public function updateField(ResponseField $field, array $data): ResponseField
    {
        $field->update($data);
        return $field;
    }

    /**
     * Delete field
     */
    public function deleteField(ResponseField $field): bool
    {
        return $field->delete();
    }

    /**
     * Reorder fields
     */
    public function reorderFields(array $items): void
    {
        foreach ($items as $item) {
            ResponseField::findOrFail($item['id'])->update(['order' => $item['order']]);
        }
    }

    /**
     * Get statistics about fields
     */
    public function getStatistics(): array
    {
        return [
            'total' => ResponseField::count(),
            'active' => ResponseField::where('is_active', true)->count(),
            'inactive' => ResponseField::where('is_active', false)->count(),
            'required' => ResponseField::where('is_required', true)->count(),
            'by_type' => ResponseField::select('field_type')
                ->selectRaw('count(*) as count')
                ->groupBy('field_type')
                ->pluck('count', 'field_type')
                ->toArray(),
        ];
    }
}
