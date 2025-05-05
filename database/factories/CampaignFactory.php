<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'description' => fake()->paragraphs(3, true),
            'goal_amount' => fake()->numberBetween(1000, 50000),
            'end_date' => fake()->dateTimeBetween('+1 month', '+1 year'),
            'image_url' => 'https://source.unsplash.com/random/800x600/?charity',
            'status' => fake()->randomElement(['active', 'active', 'active', 'paused', 'completed']), // Weighted towards active
            'creator_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'category' => fake()->randomElement(['Environmental', 'Social', 'Educational', 'Health', 'Technology']),
            'featured' => fake()->boolean(20), // 20% chance of being featured
        ];
    }

    /**
     * Indicate that the campaign is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the campaign is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }
}