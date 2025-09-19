<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\User;
use App\Models\Research;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $audits = Audit::with(['user', 'auditable'])
            ->when($request->event, fn($q) => $q->event($request->event))
            ->when($request->auditable_type, fn($q) => $q->auditableType($request->auditable_type))
            ->when($request->user_id, fn($q) => $q->byUser($request->user_id))
            ->when($request->date_from && $request->date_to, fn($q) => 
                $q->dateRange($request->date_from, $request->date_to))
            ->latest()
            ->paginate(20);

        return Inertia::render('Audits/Index', [
            'audits' => $audits,
            'filters' => $request->only(['event', 'auditable_type', 'user_id', 'date_from', 'date_to'])
        ]);
    }

    public function userActivity(User $user)
    {
        $audits = $user->audits()
            ->with('auditable')
            ->latest()
            ->paginate(20);

        return Inertia::render('Audits/UserActivity', [
            'user' => $user,
            'audits' => $audits
        ]);
    }

    public function researchChanges(Research $research)
    {
        $audits = $research->audits()
            ->with('user')
            ->latest()
            ->paginate(20);

        return Inertia::render('Audits/ResearchChanges', [
            'research' => $research,
            'audits' => $audits
        ]);
    }

    public function reports()
    {
        $stats = [
            'total_audits' => Audit::count(),
            'audits_today' => Audit::whereDate('created_at', today())->count(),
            'most_active_users' => Audit::selectRaw('user_id, count(*) as activity_count')
                ->with('user')
                ->groupBy('user_id')
                ->orderByDesc('activity_count')
                ->limit(10)
                ->get(),
            'audit_breakdown' => Audit::selectRaw('event, count(*) as count')
                ->groupBy('event')
                ->get(),
            'model_breakdown' => Audit::selectRaw('auditable_type, count(*) as count')
                ->groupBy('auditable_type')
                ->get()
        ];

        return Inertia::render('Audits/Reports', [
            'stats' => $stats
        ]);
    }
}
