<?php


namespace Database\Seeders;

use App\Models\ReviewField;
use Illuminate\Database\Seeder;

class ReviewFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            [
                'name' => 'ReviewId',
                'label' => 'Review ID',
                'description' => 'Unique identifier of the review',
            ],
            [
                'name' => 'Timestamp',
                'label' => 'Timestamp',
                'description' => 'Date and time when the review was created',
            ],
            [
                'name' => 'URL',
                'label' => 'URL',
                'description' => 'Direct URL of the review',
            ],
            [
                'name' => 'Recommendation',
                'label' => 'Recommendation',
                'description' => 'Indicates if the reviewer recommends the business',
            ],
            [
                'name' => 'IsDeleted',
                'label' => 'Is Deleted',
                'description' => 'Indicates if the review was deleted',
            ],
            [
                'name' => 'Author',
                'label' => 'Author',
                'description' => 'Author of the review',
            ],
            [
                'name' => 'Replies',
                'label' => 'Replies',
                'description' => 'Replies to the review',
            ],
            [
                'name' => 'ReplyUrl',
                'label' => 'Reply URL',
                'description' => 'URL to reply to the review',
            ],
            [
                'name' => 'Text',
                'label' => 'Text',
                'description' => 'Content of the review',
            ],
            [
                'name' => 'LastEdited',
                'label' => 'Last Edited',
                'description' => 'Last edit timestamp of the review',
            ],
            [
                'name' => 'Rating',
                'label' => 'Rating',
                'description' => 'Rating given by the reviewer',
            ],
            [
                'name' => 'IsHidden',
                'label' => 'Is Hidden',
                'description' => 'Indicates if the review is hidden',
            ],
            [
                'name' => 'Translation',
                'label' => 'Translation',
                'description' => 'Translated version of the review',
            ],
            [
                'name' => 'Photos',
                'label' => 'Photos',
                'description' => 'Photos attached to the review',
            ],
            [
                'name' => 'Edits',
                'label' => 'Edits',
                'description' => 'Edit history of the review',
            ],
        ];

        foreach ($fields as $field) {
            ReviewField::create($field);
        }
    }
}
