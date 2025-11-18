<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            AdminStaffSeeder::class,
            AgendaSeeder::class,
            FacultySeeder::class,
            KeywordSeeder::class,
            ProgramSeeder::class,
            ReportFormatSeeder::class,
            ReportTypeSeeder::class,
            ResearcherSeeder::class,
            ResearchKeywordSeeder::class,
            ResearchSeeder::class,
            RoleSeeder::class,
            SdgSeeder::class,
            SrigSeeder::class,
        ]);
    }
}
