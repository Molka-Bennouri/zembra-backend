<?php

namespace App\Services;

use App\Models\ReviewField;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ReviewFieldService
{
    /**
     * Get all fields with caching
     */
    public function getAllFields(): EloquentCollection
    {
        return ReviewField::orderBy('id')->get();
    }
}
