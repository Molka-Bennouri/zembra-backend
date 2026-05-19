<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    public function run(): void //php artisan db:seed --class=FieldSeeder
    {
        Field::truncate();

        // Listing fields (anciens ResponseField)
        $listingFields = [
            ['name' => 'AddressComponents', 'label' => 'Address Components', 'description' => 'Detailed address components breakdown'],
            ['name' => 'PageId',            'label' => 'Page ID',            'description' => 'Unique identifier for the page'],
            ['name' => 'ReviewPageSlug',    'label' => 'Review Page Slug',   'description' => 'Slug for review page URL'],
            ['name' => 'link',              'label' => 'Review Page URL',    'description' => 'Full URL for review page'],
            ['name' => 'aliases',           'label' => 'Aliases',            'description' => 'Alternative names or aliases'],
            ['name' => 'categories',        'label' => 'Categories',         'description' => 'Business categories'],
            ['name' => 'network',           'label' => 'Business Name',      'description' => 'Official business name'],
            ['name' => 'phone',             'label' => 'Phone',              'description' => 'Business phone number'],
            ['name' => 'photos',            'label' => 'Photos',             'description' => 'Business photos and images'],
            ['name' => 'priceRange',        'label' => 'Price Range',        'description' => 'Price range indicator'],
            ['name' => 'profileImage',      'label' => 'Profile Image',      'description' => 'Main profile/logo image'],
            ['name' => 'BusinessWebsite',   'label' => 'Business Website',   'description' => 'Business website URL'],
            ['name' => 'globalRating',      'label' => 'Total Review Count', 'description' => 'Total number of reviews'],
            ['name' => 'OverallRating',     'label' => 'Overall Rating',     'description' => 'Overall business rating'],
            ['name' => 'formattedAddress',  'label' => 'Formatted Address',  'description' => 'Complete formatted address'],
        ];

        // Review fields (anciens ReviewField)
        $reviewFields = [
            ['name' => 'id',             'label' => 'Review ID',     'description' => 'Unique identifier of the review'],
            ['name' => 'timestamp',      'label' => 'Timestamp',     'description' => 'Date and time when the review was created'],
            ['name' => 'url',            'label' => 'URL',           'description' => 'Direct URL of the review'],
            ['name' => 'recommendation', 'label' => 'Recommendation','description' => 'Indicates if the reviewer recommends the business'],
            ['name' => 'isDeleted',      'label' => 'Is Deleted',    'description' => 'Indicates if the review was deleted'],
            ['name' => 'author',         'label' => 'Author',        'description' => 'Author of the review'],
            ['name' => 'replies',        'label' => 'Replies',       'description' => 'Replies to the review'],
            ['name' => 'replyUrl',       'label' => 'Reply URL',     'description' => 'URL to reply to the review'],
            ['name' => 'text',           'label' => 'Text',          'description' => 'Content of the review'],
            ['name' => 'editedAt',       'label' => 'Last Edited',   'description' => 'Last edit timestamp of the review'],
            ['name' => 'rating',         'label' => 'Rating',        'description' => 'Rating given by the reviewer'],
            ['name' => 'isHidden',       'label' => 'Is Hidden',     'description' => 'Indicates if the review is hidden'],
            ['name' => 'translation',    'label' => 'Translation',   'description' => 'Translated version of the review'],
            ['name' => 'photos',         'label' => 'Photos',        'description' => 'Photos attached to the review'],
            ['name' => 'edits',          'label' => 'Edits',         'description' => 'Edit history of the review'],
        ];

        foreach ($listingFields as $field) {
            Field::create(array_merge($field, ['context' => 'listing']));
        }

        foreach ($reviewFields as $field) {
            Field::create(array_merge($field, ['context' => 'review']));
        }
    }
}
