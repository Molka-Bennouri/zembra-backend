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
                'name' => 'ReviewPageUrl',
                'label' => 'Review Page URL',
                'description' => 'Full URL for review page',
            ],
            [
                'name' => 'Aliases',
                'label' => 'Aliases',
                'description' => 'Alternative names or aliases',
            ],
            [
                'name' => 'Categories',
                'label' => 'Categories',
                'description' => 'Business categories',
            ],
            [
                'name' => 'BusinessName',
                'label' => 'Business Name',
                'description' => 'Official business name',
            ],
            [
                'name' => 'Phone',
                'label' => 'Phone',
                'description' => 'Business phone number',
            ],
            [
                'name' => 'Photos',
                'label' => 'Photos',
                'description' => 'Business photos and images',
            ],
            [
                'name' => 'PriceRange',
                'label' => 'Price Range',
                'description' => 'Price range indicator',
            ],
            [
                'name' => 'ProfileImage',
                'label' => 'Profile Image',
                'description' => 'Main profile/logo image',
            ],
            [
                'name' => 'BusinessWebsite',
                'label' => 'Business Website',
                'description' => 'Business website URL',
            ],
            [
                'name' => 'TotalReviewCount',
                'label' => 'Total Review Count',
                'description' => 'Total number of reviews',
            ],
            [
                'name' => 'OverallRating',
                'label' => 'Overall Rating',
                'description' => 'Overall business rating',
            ],
            [
                'name' => 'FormattedAddress',
                'label' => 'Formatted Address',
                'description' => 'Complete formatted address',
            ],
        ];

        foreach ($fields as $field) {
            ResponseField::create($field);
        }
    }
}
