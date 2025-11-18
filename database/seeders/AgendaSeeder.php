<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Agenda;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Agenda::firstOrCreate(
            ['name' => 'University Research Agenda'],
            ['description' => 'High-level research priorities across the university.']
        );

        Agenda::firstOrCreate(
            ['name' => 'College Research Agenda'],
            ['description' => 'Focused research directions for the college.']
        );
    }
}