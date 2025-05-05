<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 5, 1000),
            'message' => fake()->optional(0.7)->sentence(), // 70% chance of having a message
            'campaign_id' => Campaign::inRandomOrder()->first()->id ?? Campaign::factory(),
            'donor_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'payment_id' => '