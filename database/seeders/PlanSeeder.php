<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// database/seeders/PlanSeeder.php
use App\Models\Plan;


class PlanSeeder extends Seeder
{


    public function run()
    {
        Plan::create([
            'name' => 'Startup',
            'amount' => 500,
            'duration_days' => 45,
            'stripe_price_id' =>  "price_1TMQ6tRx16Qtn2b0KtfZI5rS",
            'recommended' => true,
            'features' => [
                "1,743 pages",
                "435,750 reviews",
                "$1 = 1,250 credits",
                "1 credit = $0.0008"
            ]
        ]);

        Plan::create([
            'name' => 'Basic',
            'amount' => 50,
            'duration_days' => 30,
            'stripe_price_id' =>  "price_1TMQ6uRx16Qtn2b0ggPrGnSK",
            'features' => [
                "138 pages",
                "34,500 reviews",
                "$1 = 1,000 credits",
                "1 credit = $0.0010"
            ]
        ]);
        Plan::create([
            'name' => 'Business',
            'amount' => 1000,
            'duration_days' => 45,
            'stripe_price_id' =>  "price_1TMQ6vRx16Qtn2b0E9TkaVIj",
            'features' => [
                "3,991 pages",
                "997,750 reviews",
                "$1 = 1,667 credits",
                "1 credit = $0.0006"
            ]
        ]);

        // 🚀 Enterprise plan
        Plan::create([
            'name' => 'Enterprise',
            'amount' => 5000,
            'duration_days' => 30,
            'stripe_price_id' => "price_1TMQ73Rx16Qtn2b0Oy2WRvoK",
            'features' => [
                "9,360 pages",
                "2,340,000 reviews",
                "$1 = 2,000 credits",
                "1 credit = $0.0005"
            ]
        ]);

    }
}

