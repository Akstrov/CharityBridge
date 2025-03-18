<?php

namespace Database\Factories;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Claim>
 */
class ClaimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'donation_id' => Donation::factory(),
            'charity_id' => User::factory()->charity(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'completed']),
            'notes' => $this->faker->optional(0.7)->paragraph(),
            'pickup_date' => $this->faker->optional(0.5)->dateTimeBetween('now', '+7 days'),
        ];
    }
    // Approved state
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'pickup_date' => $this->faker->dateTimeBetween('now', '+7 days'),
            ];
        });
    }

    // Completed state
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'pickup_date' => $this->faker->dateTimeBetween('-7 days', 'now'),
            ];
        });
    }
}
