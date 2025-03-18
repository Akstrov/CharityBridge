<?php

namespace Database\Factories;

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
        $category = $this->faker->randomElement(['food', 'clothes', 'money', 'other']);
        $quantity = $category == 'money' ? null : $this->faker->numberBetween(1, 100);
        $monetaryValue = $category == 'money' ? $this->faker->randomFloat(2, 10, 10000) : null;

        return [
            'user_id' => User::factory()->donor(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'category' => $category,
            'quantity' => $quantity,
            'location' => $this->faker->address(),
            'status' => $this->faker->randomElement(['available', 'claimed', 'completed']),
            'is_urgent' => $this->faker->boolean(20), // 20% chance of being urgent
            'monetary_value' => $monetaryValue,
            'expiry_date' => $category == 'food' ? $this->faker->dateTimeBetween('now', '+30 days') : null,
        ];
    }
    // Available state
    public function available()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'available',
            ];
        });
    }

    // Urgent state
    public function urgent()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_urgent' => true,
            ];
        });
    }
}
