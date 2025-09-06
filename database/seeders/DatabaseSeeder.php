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

        User::factory()->create([
            'first_name' => 'Test',
            'middle_name' => null,
            'last_name' => 'User',
            'student_id' => null,
            'contact_number' => '09123456789',
            'email' => 'test@usep.edu.ph',
            'role' => 'Administrator',
        ]);
        
        $this->call([
            RoleSeeder::class,
            FacultySeeder::class,
            ProgramSeeder::class,
            ResearchSeeder::class,
            ResearcherSeeder::class,
            KeywordSeeder::class,
        ]);
        
    }
}
