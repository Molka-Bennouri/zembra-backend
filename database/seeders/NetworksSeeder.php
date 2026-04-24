<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NetworksSeeder extends Seeder //php artisan db:seed --class=NetworksSeeder
{
    public function run(): void
    {
        DB::table('networks')->truncate();

        DB::table('networks')->insert([
            [
                'name'         => 'airbnb',
                'label'        => 'Airbnb',
                'slug_pattern' => '@^(?:(?:(?:(?:(?:https?://)?(?:[\w-]+\.)*airbnb(?:\.[a-z]{2,3}){1,2}/)?/?))?(?:[\w+-]+)?/)?([\d+-]+)?/?(?:[?#].*)?$@i',
            ],
            [
                'name'         => 'ecommunity',
                'label'        => 'Community Health Network',
                'slug_pattern' => '@^https?://(?:[\w-]+\.)*ecommunity\.[a-z.]+/(?:[^/]+/)*providers?/[A-Za-z]+/([0-9]+)/?$@i',
            ],
            [
                'name'         => 'justia',
                'label'        => 'Justia',
                'slug_pattern' => '@^(?:(?:https?://)?(?:[\w-]+\.)*justia(?:\.[a-z]{2,3}){1,2}/lawyer/)?([\w][\w-]*-\d+)(?:/[\w-]*)?(?:[?#].*)?$@i',
            ],
            [
                'name'         => 'kununu',
                'label'        => 'Kununu',
                'slug_pattern' => '@^(?:(?:(?:https?://)?(?:www\.)?kununu\.com)?/)?(?:de/)?([a-z][a-z0-9-]*[a-z0-9])$@',
            ],
            [
                'name'         => 'lawtally',
                'label'        => 'Lawtally',
                'slug_pattern' => '@^(?:(?:https?:\/\/)?(?:[\w-]+\.)*lawtally(?:\.[a-z]{2,3}){1,2}\/)?(?:lawyers\/)?([\w-]+)\/?(?:[?#].*)?$@i',
            ],
            [
                'name'         => 'ssmhealth',
                'label'        => 'SSM Health',
                'slug_pattern' => '@^(?:https?://)?(?:[\w-]+\.)?getcare\.ssmhealth(?:\.[a-z]{2,3}){1,2}/?(?:find-a-doctor/doctor-details/|doctor-details/)?([a-z0-9]+(?:-[a-z0-9]+)*)/?(?:[?#].*)?$@i',
            ],
            [
                'name'         => 'viator',
                'label'        => 'Viator',
                'slug_pattern' => '@^https?://(?:www\.)?viator\.com/tours/[A-Za-z0-9-]+/[A-Za-z0-9-]+/d\d+-\d+[A-Z]\d+$@i',
            ],
        ]);
    }
}
