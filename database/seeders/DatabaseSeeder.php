<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'firstName' => 'Test',
            'middleName' => null,
            'lastName' => 'User',
            'studentID' => null,
            'contactNumber' => '09123456789',
            'email' => 'test@usep.edu.ph',
            'role' => 'Administrator',
        ]);

        // Add this line to run the FacultySeeder
        $this->call(FacultySeeder::class);
    }
}
