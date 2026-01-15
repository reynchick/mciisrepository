<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Traits\HandlesLogFiltering;
use App\Models\UserAuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserAuditLogController extends Controller
{
    use HandlesLogFiltering;

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', UserAuditLog::class);

        $logs = UserAuditLog::with([
            'modifiedBy:id,first_name,last_name,email',
            'targetUser:id,first_name,last_name,email',
        ])
            ->when($request->filled('modified_by'), fn ($q) => $q->where('modified_by', $request->modified_by))
            ->when($request->filled('target_user_id'), fn ($q) => $q->where('target_user_id', $request->target_user_id))
            ->when($request->filled('action_type'), fn ($q) => $q->where('action_type', $request->action_type));

        $logs = $this->applyDateFilters($logs, $request->from, $request->to)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Logs/UserAudits', [
            'logs' => $logs,
            'filters' => $request->only(['modified_by', 'target_user_id', 'action_type', 'from', 'to']),
            'actionTypes' => UserAuditLog::getActionTypes(),
        ]);
    }
}
