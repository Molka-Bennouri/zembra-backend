<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewFieldSeeder extends Seeder //php artisan db:seed --class=ReviewFieldSeeder
{
    public function run(): void
    {
        DB::table('review_fields')->truncate();

        DB::table('review_fields')->insert([
            [
                'name' => 'id',
                'label' => 'Review ID',
                'description' => 'Unique identifier of the review',
            ],
            [
                'name' => 'timestamp',
                'label' => 'Timestamp',
                'description' => 'Date and time when the review was created',
            ],
            [
                'name' => 'url',
                'label' => 'URL',
                'description' => 'Direct URL of the review',
            ],
            [
                'name' => 'recommendation',
                'label' => 'Recommendation',
                'description' => 'Indicates if the reviewer recommends the business',
            ],
            [
                'name' => 'isDeleted',
                'label' => 'Is Deleted',
                'description' => 'Indicates if the review was deleted',
            ],
            [
                'name' => 'author',
                'label' => 'Author',
                'description' => 'Author of the review',
            ],
            [
                'name' => 'replies',
                'label' => 'Replies',
                'description' => 'Replies to the review',
            ],
            [
                'name' => 'replyUrl',
                'label' => 'Reply URL',
                'description' => 'URL to reply to the review',
            ],
            [
                'name' => 'text',
                'label' => 'Text',
                'description' => 'Content of the review',
            ],
            [
                'name' => 'editedAt',
                'label' => 'Last Edited',
                'description' => 'Last edit timestamp of the review',
            ],
            [
                'name' => 'rating',
                'label' => 'Rating',
                'description' => 'Rating given by the reviewer',
            ],
            [
                'name' => 'isHidden',
                'label' => 'Is Hidden',
                'description' => 'Indicates if the review is hidden',
            ],
            [
                'name' => 'translation',
                'label' => 'Translation',
                'description' => 'Translated version of the review',
            ],
            [
                'name' => 'photos',
                'label' => 'Photos',
                'description' => 'Photos attached to the review',
            ],
            [
                'name' => 'edits',
                'label' => 'Edits',
                'description' => 'Edit history of the review',
            ],
        ]);
    }
}
