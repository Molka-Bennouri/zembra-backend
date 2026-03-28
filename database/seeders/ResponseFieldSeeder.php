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
            ],
            [
                'name' => 'PageId',
                'label' => 'Page ID',
                'description' => 'Unique identifier for the page',
            ],
            [
                'name' => 'ReviewPageSlug',
                'label' => 'Review Page Slug',
                'description' => 'Slug for review page URL',
            ],
            [
                'name' => 'link',
                'label' => 'Review Page URL',
                'description' => 'Full URL for review page',
            ],
            [
                'name' => 'aliases',
                'label' => 'Aliases',
                'description' => 'Alternative names or aliases',
            ],
            [
                'name' => 'categories',
                'label' => 'Categories',
                'description' => 'Business categories',
            ],
            [
                'name' => 'network',
                'label' => 'Business Name',
                'description' => 'Official business name',
            ],
            [
                'name' => 'phone',
                'label' => 'Phone',
                'description' => 'Business phone number',
            ],
            [
                'name' => 'photos',
                'label' => 'Photos',
                'description' => 'Business photos and images',
            ],
            [
                'name' => 'priceRange',
                'label' => 'Price Range',
                'description' => 'Price range indicator',
            ],
            [
                'name' => 'profileImage',
                'label' => 'Profile Image',
                'description' => 'Main profile/logo image',
            ],
            [
                'name' => 'BusinessWebsite',
                'label' => 'Business Website',
                'description' => 'Business website URL',
            ],
            [
                'name' => 'globalRating',
                'label' => 'Total Review Count',
                'description' => 'Total number of reviews',
            ],
            [
                'name' => 'OverallRating',
                'label' => 'Overall Rating',
                'description' => 'Overall business rating',
            ],
            [
                'name' => 'formattedAddress',
                'label' => 'Formatted Address',
                'description' => 'Complete formatted address',
            ],
        ];

        foreach ($fields as $field) {
            ResponseField::create($field);
        }
    }
}
