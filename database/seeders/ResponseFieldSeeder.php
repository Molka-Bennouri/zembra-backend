<?php


namespace Database\Seeders;

use App\Models\ResponseField;
use Illuminate\Database\Seeder;

class ResponseFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            [
                'name' => 'AddressComponents',
                'label' => 'Address Components',
                'description' => 'Detailed address components breakdown',
                'field_type' => 'object',
                'is_active' => true,
                'is_required' => false,
                'order' => 1,
            ],
            [
                'name' => 'PageId',
                'label' => 'Page ID',
                'description' => 'Unique identifier for the page',
                'field_type' => 'string',
                'is_active' => true,
                'is_required' => false,
                'order' => 2,
            ],
            [
                'name' => 'ReviewPageSlug',
                'label' => 'Review Page Slug',
                'description' => 'Slug for review page URL',
                'field_type' => 'string',
                'is_active' => true,
                'is_required' => false,
                'order' => 3,
            ],
            [
                'name' => 'ReviewPageUrl',
                'label' => 'Review Page URL',
                'description' => 'Full URL for review page',
                'field_type' => 'string',
                'is_active' => true,
                'is_required' => false,
                'order' => 4,
            ],
            [
                'name' => 'Aliases',
                'label' => 'Aliases',
                'description' => 'Alternative names or aliases',
                'field_type' => 'array',
                'is_active' => true,
                'is_required' => false,
                'order' => 5,
            ],
            [
                'name' => 'Categories',
                'label' => 'Categories',
                'description' => 'Business categories',
                'field_type' => 'array',
                'is_active' => true,
                'is_required' => false,
                'order' => 6,
            ],
            [
                'name' => 'BusinessName',
                'label' => 'Business Name',
                'description' => 'Official business name',
                'field_type' => 'string',
                'is_active' => true,
                'is_required' => true,
                'order' => 7,
            ],
            [
                'name' => 'Phone',
                'label' => 'Phone',
                'description' => 'Business phone number',
                'field_type' => 'string',
                'is_active' => true,
                'is_required' => false,
                'order' => 8,
            ],
            [
                'name' => 'Photos',
                'label' => 'Photos',
                'description' => 'Business photos and images',
                'field_type' => 'array',
                'is_active' => true,
                'is_required' => false,
                'order' => 9,
            ],
            [
                'name' => 'PriceRange',
                'label' => 'Price Range',
                'description' => 'Price range indicator',
                'field_type' => 'string',
                'is_active' => true,
                'is_required' => false,
                'order' => 10,
            ],
            [
                'name' => 'ProfileImage',
                'label' => 'Profile Image',
                'description' => 'Main profile/logo image',
                'field_type' => 'string',
                'is_active' => true,
                'is_required' => false,
                'order' => 11,
            ],
            [
                'name' => 'BusinessWebsite',
                'label' => 'Business Website',
                'description' => 'Business website URL',
                'field_type' => 'string',
                'is_active' => true,
                'is_required' => false,
                'order' => 12,
            ],
            [
                'name' => 'TotalReviewCount',
                'label' => 'Total Review Count',
                'description' => 'Total number of reviews',
                'field_type' => 'number',
                'is_active' => true,
                'is_required' => false,
                'order' => 13,
            ],
            [
                'name' => 'OverallRating',
                'label' => 'Overall Rating',
                'description' => 'Overall business rating',
                'field_type' => 'number',
                'is_active' => true,
                'is_required' => false,
                'order' => 14,
            ],
            [
                'name' => 'FormattedAddress',
                'label' => 'Formatted Address',
                'description' => 'Complete formatted address',
                'field_type' => 'string',
                'is_active' => true,
                'is_required' => false,
                'order' => 15,
            ],
        ];

        foreach ($fields as $field) {
            ResponseField::create($field);
        }
    }
}
