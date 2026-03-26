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
                'name'         => 'Airbnb',
                'slug_pattern' => '@^(?:(?:(?:(?:(?:https?://)?(?:[\w-]+\.)*airbnb(?:\.[a-z]{2,3}){1,2}/)?/?))?(?:[\w+-]+)?/)?([\d+-]+)?/?(?:[?#].*)?$@i',
            ],
            [
                'name'         => 'Community Health Network',
                'slug_pattern' => '@^https?://(?:[\w-]+\.)*ecommunity\.[a-z.]+/(?:[^/]+/)*providers?/[A-Za-z]+/([0-9]+)/?$@i',
            ],
            [
                'name'         => 'Kununu',
                'slug_pattern' => '@^(?:(?:(?:https?://)?(?:www\.)?kununu\.com)?/)?(?:de/)?([a-z][a-z0-9-]*[a-z0-9])$@',
            ],
            [
                'name'         => 'Viator',
                'slug_pattern' => '@^https?://(?:www\.)?viator\.com/tours/[A-Za-z0-9-]+/[A-Za-z0-9-]+/d\d+-\d+[A-Z]\d+$@i',
            ],
        ]);
    }
}
