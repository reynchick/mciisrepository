<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Research;

class ReportService
{
    /**
     * Returns a map of role name => user count.
     */
    public function userRoleDistribution(): array
    {
        return Role::query()
            ->withCount('users')
            ->orderBy('users_count', 'desc')
            ->get()
            ->pluck('users_count', 'name')
            ->toArray();
    }

    /**
     * Returns top accessed research records ordered by access logs count.
     */
    public function topAccessedResearch(int $limit = 5)
    {
        return Research::query()
            ->withCount('accessLogs')
            ->orderBy('access_logs_count', 'desc')
            ->limit($limit)
            ->get();
    }
}