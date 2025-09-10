<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Research>
 */
class ResearchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uploaded_by'             => User::factory(),
            'research_title'          => fake()->unique()->sentence(6),
            'research_adviser'        => null,
            'program_id'              => Program::query()->inRandomOrder()->firstOrFail()->id,
            'published_month'         => fake()->optional()->numberBetween(1, 12),
            'published_year'          => fake()->numberBetween(2015, (int) now()->year),
            'research_abstract'       => fake()->paragraphs(nb: 3, asText: true),
            'research_approval_sheet' => 'storage/research/approval_sheets/' . fake()->unique()->uuid() . '.jpg',
            'research_manuscript'     => 'storage/research/manuscripts/' . fake()->unique()->uuid() . '.pdf',
            'archived_at'             => null,
            'archived_by'             => null,
            'archive_reason'          => null,
        ];
    }
}