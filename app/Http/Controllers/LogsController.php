<?php

namespace App\Http\Controllers;

use App\Models\KeywordSearchLog;
use App\Models\ResearchAccessLog;
use App\Models\UserAuditLog;
use App\Models\FacultyAuditLog;
use App\Models\ResearchEntryLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class LogsController extends Controller
{
    /**
     * Authorize access to logs for admin or staff users.
     */
    protected function authorizeLogs(): void
    {
        $user = auth()->user->id();

        if (!$user || !$user->isAdminOrStaff()) {
            abort(403);
        }
    }

    /**
     * Display a listing of research access logs.
     */
    public function researchAccess(Request $request): Response
    {
        $this->authorizeLogs();

        $logs = ResearchAccessLog::with([
                'user:id,first_name,last_name,email',
                'research:id,research_title',
            ])
            ->when($request->filled('user'), fn ($q) => $q->where('user_id', $request->user))
            ->when($request->filled('research'), fn ($q) => $q->where('research_id', $request->research))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Logs/ResearchAccess', [
            'logs' => $logs,
            'filters' => $request->only(['user', 'research', 'from', 'to']),
        ]);
    }

    /**
     * Display a listing of keyword search logs.
     */
    public function keywordSearch(Request $request): Response
    {
        $this->authorizeLogs();

        $logs = KeywordSearchLog::with([
                'user:id,first_name,last_name,email',
                'keyword:id,keyword_name',
            ])
            ->when($request->filled('user'), fn ($q) => $q->where('user_id', $request->user))
            ->when($request->filled('keyword'), fn ($q) => $q->where('keyword_id', $request->keyword))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Logs/KeywordSearch', [
            'logs' => $logs,
            'filters' => $request->only(['user', 'keyword', 'from', 'to']),
        ]);
    }

    /**
     * Display a listing of user audit logs.
     */
    public function userAudits(Request $request): Response
    {
        $this->authorizeLogs();

        $logs = UserAuditLog::with([
                'modifiedBy:id,first_name,last_name,email',
                'targetUser:id,first_name,last_name,email',
            ])
            ->when($request->filled('modifiedBy'), fn ($q) => $q->where('modifiedBy', $request->modifiedBy))
            ->when($request->filled('targetUserID'), fn ($q) => $q->where('targetUserID', $request->targetUserID))
            ->when($request->filled('actionType'), fn ($q) => $q->where('actionType', $request->actionType))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Logs/UserAudits', [
            'logs' => $logs,
            'filters' => $request->only(['modifiedBy', 'targetUserID', 'actionType', 'from', 'to']),
        ]);
    }

    /**
     * Display a listing of faculty audit logs.
     */
    public function facultyAudits(Request $request): Response
    {
        $this->authorizeLogs();

        $logs = FacultyAuditLog::with([
                'modifiedBy:id,first_name,last_name,email',
                'targetFaculty:id,faculty_id,first_name,last_name,email',
            ])
            ->when($request->filled('modifiedBy'), fn ($q) => $q->where('modifiedBy', $request->modifiedBy))
            ->when($request->filled('targetFacultyID'), fn ($q) => $q->where('targetFacultyID', $request->targetFacultyID))
            ->when($request->filled('actionType'), fn ($q) => $q->where('actionType', $request->actionType))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Logs/FacultyAudits', [
            'logs' => $logs,
            'filters' => $request->only(['modifiedBy', 'targetFacultyID', 'actionType', 'from', 'to']),
        ]);
    }

    /**
     * Display a listing of research entry logs.
     */
    public function researchEntries(Request $request): Response
    {
        $this->authorizeLogs();

        $logs = ResearchEntryLog::with([
                'modifiedBy:id,first_name,last_name,email',
                'targetResearch:id,research_title',
            ])
            ->when($request->filled('modifiedBy'), fn ($q) => $q->where('modifiedBy', $request->modifiedBy))
            ->when($request->filled('targetResearchID'), fn ($q) => $q->where('targetResearchID', $request->targetResearchID))
            ->when($request->filled('actionType'), fn ($q) => $q->where('actionType', $request->actionType))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Logs/ResearchEntries', [
            'logs' => $logs,
            'filters' => $request->only(['modifiedBy', 'targetResearchID', 'actionType', 'from', 'to']),
        ]);
    }

    /**
     * Display top accessed research statistics.
     */
    public function topAccessedResearch(Request $request): JsonResponse
    {
        $this->authorizeLogs();

        $top = ResearchAccessLog::selectRaw('research_id, COUNT(*) as accesses')
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->groupBy('research_id')
            ->orderByDesc('accesses')
            ->limit(5)
            ->with('research:id,research_title')
            ->get();

        return response()->json($top);
    }

    /**
     * Display top keywords statistics.
     */
    public function topKeywords(Request $request): JsonResponse
    {
        $this->authorizeLogs();

        $top = KeywordSearchLog::selectRaw('keyword_id, COUNT(*) as searches')
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->groupBy('keyword_id')
            ->orderByDesc('searches')
            ->limit(5)
            ->with('keyword:id,keyword_name')
            ->get();

        return response()->json($top);
    }
}