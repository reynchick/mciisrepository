<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Traits\HandlesLogFiltering;
use App\Models\ResearchAccessLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResearchAccessLogController extends Controller
{
    use HandlesLogFiltering;

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', ResearchAccessLog::class);

        $logs = ResearchAccessLog::with([
            'user:id,first_name,last_name,email',
            'research:id,research_title',
        ]);

        $logs = $this->applyUserFilters($logs, $request->user);

        if ($request->filled('research')) {
            $logs->where('research_id', $request->research);
        }

        $logs = $this->applyDateFilters($logs, $request->from, $request->to)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Logs/ResearchAccess', [
            'logs' => $logs,
            'filters' => $request->only(['user', 'research', 'from', 'to']),
        ]);
    }
}
