<?php

namespace Database\Seeders;

use App\Models\ReportType;
use Illuminate\Database\Seeder;

class ReportTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Abstract Compilation',
                'description' => 'Compilation of research abstracts'
            ],
            [
                'name' => 'Executive Summary Compilation',
                'description' => 'Compilation of executive summaries'
            ],
            [
                'name' => 'Matrix/Tabular Report',
                'description' => 'Tabular representation of research data'
            ],
        ];

        foreach ($types as $type) {
            ReportType::create($type);
        }
    }
}