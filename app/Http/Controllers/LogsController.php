<?php

namespace App\Http\Controllers;

use App\Models\KeywordSearchLog;
use App\Models\ResearchAccessLog;
use App\Models\UserAuditLog;
use App\Models\FacultyAuditLog;
use App\Models\ResearchEntryLog;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserAuditLogRequest;
use App\Http\Requests\StoreFacultyAuditLogRequest;
use App\Http\Requests\StoreResearchEntryLogRequest;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LogsController extends Controller
{
    public function createUserLog()
    {
        return Inertia::render('Logs/CreateUserLog', [
            'options' => array_keys(UserAuditLog::getActionTypes()),
            'labels' => UserAuditLog::getActionTypes(),
        ]);
    }

    public function storeUserLog(StoreUserAuditLogRequest $request)
    {
        UserAuditLog::create($request->validated());
        return redirect()->back()->with('success', 'User log recorded.');
    }
    
    /**
     * Create faculty audit log form
     */
    public function createFacultyLog()
    {
        return Inertia::render('Logs/CreateFacultyLog', [
            'options' => array_keys(FacultyAuditLog::getActionTypes()),
            'labels' => FacultyAuditLog::getActionTypes(),
        ]);
    }

    /**
     * Store a new faculty audit log
     */
    public function storeFacultyLog(StoreFacultyAuditLogRequest $request)
    {
        FacultyAuditLog::create($request->validated());
        return redirect()->back()->with('success', 'Faculty log recorded.');
    }

    /**
     * Create research entry log form
     */
    public function createResearchEntryLog()
    {
        return Inertia::render('Logs/CreateResearchEntryLog', [
        'options' => array_keys(ResearchEntryLog::getActionTypes()),
        'labels' => ResearchEntryLog::getActionTypes(),
        ]);
    }

    /**
     * Store a new research entry log
     */
    public function storeResearchEntryLog(StoreResearchEntryLogRequest $request)
    {
        ResearchEntryLog::create($request->validated());
        return redirect()->back()->with('success', 'Research entry log recorded.');
    }
    
    /**
     * Authorize access to logs for admin or staff users.
     */
    protected function authorizeLogs(): void
    {
        $user = Auth::user();

        if (!($user instanceof \App\Models\User) || !$user->isAdminOrStaff()) {
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
            ->when($request->filled('modified_by'), fn ($q) => $q->where('modified_by', $request->modified_by))
            ->when($request->filled('target_user_id'), fn ($q) => $q->where('target_user_id', $request->target_user_id))
            ->when($request->filled('action_type'), fn ($q) => $q->where('action_type', $request->action_type))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Logs/UserAudits', [
            'logs' => $logs,
            'filters' => $request->only(['modified_by', 'target_user_id', 'action_type', 'from', 'to']),
            'actionTypes' => UserAuditLog::allowedActions(),
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
            ->when($request->filled('modified_by'), fn ($q) => $q->where('modified_by', $request->modified_by))
            ->when($request->filled('target_faculty_id'), fn ($q) => $q->where('target_faculty_id', $request->target_faculty_id))
            ->when($request->filled('action_type'), fn ($q) => $q->where('action_type', $request->action_type))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Logs/FacultyAudits', [
            'logs' => $logs,
            'filters' => $request->only(['modified_by', 'target_faculty_id', 'action_type', 'from', 'to']),
            'actionTypes' => FacultyAuditLog::getActionTypes(),
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
            ->when($request->filled('modified_by'), fn ($q) => $q->where('modified_by', $request->modified_by))
            ->when($request->filled('target_research_id'), fn ($q) => $q->where('target_research_id', $request->target_research_id))
            ->when($request->filled('action_type'), fn ($q) => $q->where('action_type', $request->action_type))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Logs/ResearchEntries', [
            'logs' => $logs,
            'filters' => $request->only(['modified_by', 'target_research_id', 'action_type', 'from', 'to']),
            'actionTypes' => ResearchEntryLog::getActionTypes(),
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