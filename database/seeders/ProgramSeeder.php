<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            'Bachelor of Science in Information Technology',
            'Bachelor of Science in Computer Science',
            'Bachelor of Library and Information Science',
            'Master of Library and Information Science',
            'Master in Information Technology',
        ];

        foreach ($programs as $program) {
            Program::create([
                'name' => $program
            ]);
        }
    }
}
