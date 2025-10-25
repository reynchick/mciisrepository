<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait GeneratesReports
{
    /**
     * Generate user role distribution report.
     */
    public function generateUserRoleDistribution(): array
    {
        return $this->role()
            ->join('users', 'roles.id', '=', 'users.role_id')
            ->selectRaw('roles.name, COUNT(users.id) as count')
            ->groupBy('roles.id', 'roles.name')
            ->get()
            ->pluck('count', 'name')
            ->toArray();
    }

    /**
     * Generate top accessed research.
     */
    public function generateTopAccessedResearch(int $limit = 5)
    {
        return $this->researches()
            ->withCount('accessLogs')
            ->orderBy('access_logs_count', 'desc')
            ->limit($limit)
            ->get();
    }
}