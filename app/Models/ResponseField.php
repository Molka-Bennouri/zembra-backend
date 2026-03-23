<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseField extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'description',
        'field_type',
        'is_active',
        'is_required',
        'order',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_required' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get only active fields
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get fields grouped by category
     */
    public static function getGroupedByType()
    {
        return self::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->groupBy('field_type');
    }

    /**
     * Check if field is visible
     */
    public function isVisible(): bool
    {
        return $this->is_active;
    }
}
