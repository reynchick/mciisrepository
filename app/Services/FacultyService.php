<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Research;
use App\Models\Faculty;
use Illuminate\Support\Collection;

class FacultyService
{
    public function getFacultyStatistics(Faculty $faculty): array
    {
        return [
            'total_researches' => $faculty->advisedResearches()->count(),
            'active_researches' => $faculty->advisedResearches()->whereNull('archived_at')->count(),
            'by_program' => $faculty->advisedResearches()
                ->select('program_id')
                ->with('program')
                ->get()
                ->groupBy('program.name')
                ->map->count(),
            'by_year' => $faculty->advisedResearches()
                ->select('published_year')
                ->get()
                ->groupBy('published_year')
                ->map->count()
        ];
    }
}