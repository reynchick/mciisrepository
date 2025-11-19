<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Agenda;
use App\Models\Sdg;
use App\Models\Srig;
use App\Models\Research;
use Illuminate\Support\Carbon;

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

    public function configure(): static
    {
        return $this->afterCreating(function (Research $research) {
            $now = Carbon::now();

            $agendas = Agenda::query()->inRandomOrder()->limit(random_int(1, 3))->pluck('id')->all();
            $sdgs = Sdg::query()->inRandomOrder()->limit(random_int(1, 3))->pluck('id')->all();
            $srigs = Srig::query()->inRandomOrder()->limit(random_int(1, 3))->pluck('id')->all();

            $map = function (array $ids) use ($now) {
                return collect($ids)->mapWithKeys(fn ($id) => [$id => ['created_at' => $now, 'updated_at' => $now]])->all();
            };

            $research->agendas()->syncWithoutDetaching($map($agendas));
            $research->sdgs()->syncWithoutDetaching($map($sdgs));
            $research->srigs()->syncWithoutDetaching($map($srigs));
        });
    }
}