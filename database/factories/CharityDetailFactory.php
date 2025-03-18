<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CharityDetail>
 */
class CharityDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['education', 'health', 'environment', 'poverty', 'disaster_relief', 'animals', 'human_rights', 'community_development'];

        return [
            'user_id' => User::factory()->charity(),
            'organization_name' => $this->faker->company(),
            'organization_logo' => null,
            'mission_statement' => $this->faker->sentence(10),
            'description' => $this->faker->paragraphs(3, true),
            'website' => $this->faker->url(),
            'registration_number' => $this->faker->numerify('REG-####-####'),
            'verified' => $this->faker->boolean(70), // 70% chance of being verified
            'category' => $this->faker->randomElement($categories),
            'tax_id' => $this->faker->numerify('TAX-####-####'),
            'areas_of_focus' => $this->faker->words(4, true),
            'year_established' => $this->faker->numberBetween(1950, 2023),
            'social_media_links' => $this->faker->url(),
        ];
    }
    // Verified state
    public function verified()
    {
        return $this->state(function (array $attributes) {
            return [
                'verified' => true,
            ];
        });
    }
}
