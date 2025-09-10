<?php

namespace Database\Factories;

use App\Models\Research;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Researcher>
 */
class ResearcherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'research_id' => Research::factory(),
            'first_name'  => fake()->firstName(),
            'middle_name' => fake()->optional()->randomLetter() . '.',
            'last_name'   => fake()->lastName(),
            'email'       => fake()->optional()->unique()->safeEmail(),
        ];
    }
}