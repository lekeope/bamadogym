<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@bamadogym.com',
            'role' => 'admin',
            'checkin_token' => Str::random(32),
        ]);

        $plans = [
            [
                'name' => 'Monthly',
                'slug' => 'monthly',
                'description' => 'Full gym access for one month.',
                'price' => 1500000, // ₦15,000 in kobo
                'duration_days' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Quarterly',
                'slug' => 'quarterly',
                'description' => 'Full gym access for three months.',
                'price' => 4000000, // ₦40,000 in kobo
                'duration_days' => 90,
                'is_active' => true,
            ],
            [
                'name' => 'Annual',
                'slug' => 'annual',
                'description' => 'Full gym access for a full year.',
                'price' => 14000000, // ₦140,000 in kobo
                'duration_days' => 365,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
