<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Donation;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@acmecorp.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department' => 'IT',
            'employee_id' => 'ACME001',
        ]);

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@acmecorp.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'department' => 'Marketing',
            'employee_id' => 'ACME002',
        ]);

        // Create categories
        $categories = [
            ['name' => 'Environmental', 'slug' => 'environmental', 'description' => 'Environmental causes and initiatives', 'icon' => 'leaf'],
            ['name' => 'Social', 'slug' => 'social', 'description' => 'Social causes and community support', 'icon' => 'users'],
            ['name' => 'Educational', 'slug' => 'educational', 'description' => 'Educational initiatives and scholarships', 'icon' => 'book'],
            ['name' => 'Health', 'slug' => 'health', 'description' => 'Health and wellness initiatives', 'icon' => 'heart'],
            ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Technology and innovation projects', 'icon' => 'laptop'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create default settings
        $settings = [
            ['key' => 'site_name', 'value' => 'ACME Donation Platform', 'group' => 'general', 'is_public' => true],
            ['key' => 'site_description', 'value' => 'ACME Corp employee donation platform', 'group' => 'general', 'is_public' => true],
            ['key' => 'currency', 'value' => 'USD', 'group' => 'donation', 'is_public' => true],
            ['key' => 'min_donation', 'value' => '5', 'group' => 'donation', 'is_public' => true],
            ['key' => 'max_donation', 'value' => '10000', 'group' => 'donation', 'is_public' => true],
            ['key' => 'payment_provider', 'value' => 'dummy', 'group' => 'payment', 'is_public' => false],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        // Create sample campaigns and donations if in development environment
        if (app()->environment('local')) {
            // Create 10 sample campaigns
            Campaign::factory(10)->create();
            
            // Create 50 sample donations
            Donation::factory(50)->create();
        }
    }
}