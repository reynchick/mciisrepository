<?php

namespace Database\Seeders;

use App\Models\SRIG;
use Illuminate\Database\Seeder;

class SRIGSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $srigs = [
            [
                'name' => 'Artificial Intelligence & Machine Learning',
                'description' => 'Research focusing on AI, ML, and their applications'
            ],
            [
                'name' => 'Sustainable Agriculture',
                'description' => 'Research on sustainable farming practices and agricultural innovation'
            ],
            // Add more SRIGs as needed
        ];

        foreach ($srigs as $srig) {
            SRIG::create($srig);
        }
    }
}
