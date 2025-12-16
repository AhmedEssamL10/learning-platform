<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        DB::table('subscription_plans')->insert([
            [
                'name' => 'Basic Plan',
                'price' => 100,
                'duration' => 30,
                'description' => 'one month',
            ],
            [
                'name' => 'Standard Plan',
                'price' => 250,
                'duration' => 90,
                'description' => 'three months',
            ],
            [
                'name' => 'Premium Plan',
                'price' => 2000,
                'duration' => 365,
                'description' => 'one year',
            ],
        ]);
    }
}