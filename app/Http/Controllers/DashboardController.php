<?php


namespace App\Http\Controllers;


use App\Models\Research;
use App\Models\Program;
use App\Models\Faculty;
use App\Models\User;
use App\Models\UserAuditLog;
use App\Models\FacultyAuditLog;
use App\Models\ResearchEntryLog;
use App\Models\ResearchAccessLog;
use App\Models\KeywordSearchLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function __construct()
    {
        // Apply admin middleware only to the index method (dashboard)
        $this->middleware(function ($request, $next) {
            if (!$request->user() || !$request->user()->isAdministrator()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        })->only('index');
    }


    public function index(Request $request): Response
    {
        // Get statistics
        $stats = [
            'total_research' => Research::count(),
            'active_research' => Research::active()->count(),
            'total_programs' => Program::count(),
            'total_faculty' => Faculty::count(),
            'total_users' => User::count(),
        ];


        // Recent activity logs
        $recentActivities = collect([])
            ->concat(
                UserAuditLog::with('modifiedBy:id,first_name,last_name')
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn($log) => [
                        'type' => 'user',
                        'action' => $log->action_type,
                        'user' => $log->modifiedBy->first_name . ' ' . $log->modifiedBy->last_name,
                        'created_at' => $log->created_at,
                    ])
            )
            ->concat(
                ResearchEntryLog::with('modifiedBy:id,first_name,last_name')
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn($log) => [
                        'type' => 'research',
                        'action' => $log->action_type,
                        'user' => $log->modifiedBy->first_name . ' ' . $log->modifiedBy->last_name,
                        'created_at' => $log->created_at,
                    ])
            )
            ->sortByDesc('created_at')
            ->take(10)
            ->values();


        // Top accessed research
        $topResearch = ResearchAccessLog::select('research_id', DB::raw('COUNT(*) as access_count'))
            ->groupBy('research_id')
            ->orderByDesc('access_count')
            ->with('research:id,research_title')
            ->take(5)
            ->get()
            ->map(fn($log) => [
                'title' => $log->research->research_title ?? 'Unknown',
                'count' => $log->access_count,
            ]);


        // Top keywords
        $topKeywords = KeywordSearchLog::select('keyword_id', DB::raw('COUNT(*) as search_count'))
            ->groupBy('keyword_id')
            ->orderByDesc('search_count')
            ->with('keyword:id,keyword_name')
            ->take(5)
            ->get()
            ->map(fn($log) => [
                'name' => $log->keyword->keyword_name ?? 'Unknown',
                'count' => $log->search_count,
            ]);


        // College View - Research per program with SDG, SRIG, and Agenda breakdowns
        $collegeView = Program::select('programs.id', 'programs.name')
            ->withCount('researches')
            ->with([
                'researches' => function ($query) {
                    $query->with(['sdgs', 'srigs', 'agendas']);
                }
            ])
            ->get()
            ->map(function ($program) {
                $totalResearch = $program->researches->count();
               
                // Count unique SDGs, SRIGs, and Agendas
                $sdgCount = $program->researches->flatMap->sdgs->unique('id')->count();
                $srigCount = $program->researches->flatMap->srigs->unique('id')->count();
                $agendaCount = $program->researches->flatMap->agendas->unique('id')->count();
               
                // Calculate percentages (avoid division by zero)
                $total = $sdgCount + $srigCount + $agendaCount;
               
                return [
                    'program' => $program->name,
                    'count' => $totalResearch,
                    'sdg_percentage' => $total > 0 ? round(($sdgCount / $total) * 100, 1) : 0,
                    'srig_percentage' => $total > 0 ? round(($srigCount / $total) * 100, 1) : 0,
                    'agenda_percentage' => $total > 0 ? round(($agendaCount / $total) * 100, 1) : 0,
                ];
            })
            ->filter(fn($item) => $item['count'] > 0) // Only include programs with research
            ->values();


        // Program View - Research per year with SDG, SRIG, and Agenda breakdowns
        $programView = Research::with(['sdgs', 'srigs', 'agendas'])
            ->get()
            ->groupBy('published_year')
            ->map(function ($researches, $year) {
                $totalResearch = $researches->count();
               
                // Count unique SDGs, SRIGs, and Agendas
                $sdgCount = $researches->flatMap->sdgs->unique('id')->count();
                $srigCount = $researches->flatMap->srigs->unique('id')->count();
                $agendaCount = $researches->flatMap->agendas->unique('id')->count();
               
                // Calculate percentages (avoid division by zero)
                $total = $sdgCount + $srigCount + $agendaCount;
               
                return [
                    'year' => $year,
                    'count' => $totalResearch,
                    'sdg_percentage' => $total > 0 ? round(($sdgCount / $total) * 100, 1) : 0,
                    'srig_percentage' => $total > 0 ? round(($srigCount / $total) * 100, 1) : 0,
                    'agenda_percentage' => $total > 0 ? round(($agendaCount / $total) * 100, 1) : 0,
                ];
            })
            ->filter(fn($item) => $item['year'] !== null) // Only include researches with year
            ->sortBy('year')
            ->values();


        return Inertia::render('dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'topResearch' => $topResearch,
            'topKeywords' => $topKeywords,
            'collegeView' => $collegeView,
            'programView' => $programView,
        ]);
    }


    public function home(Request $request): Response
    {
        $query = Research::with(['program', 'adviser'])
            ->active(); // Only show non-archived research


        // Search by title or keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('research_title', 'LIKE', "%{$search}%")
                  ->orWhereHas('keywords', function($keywordQuery) use ($search) {
                      $keywordQuery->where('keyword', 'LIKE', "%{$search}%");
                  });
            });
        }


        // Use the scopes from Research model
        if ($request->filled('year')) {
            $query->byYear($request->year);
        }


        if ($request->filled('adviser')) {
            $query->byAdviser($request->adviser);
        }


        if ($request->filled('program')) {
            $query->byProgram($request->program);
        }


        $researches = $query->latest()->paginate(10)->toArray();


        return Inertia::render('Home', [
            'researches' => $researches['data'],
            'programmes' => Program::select('id', 'name')->get(),
            'advisers' => Faculty::select('id', 'first_name', 'middle_name', 'last_name')
                ->orderBy('last_name')
                ->get(),
            'filters' => $request->only(['search', 'year', 'adviser', 'program']),
        ]);
    }
}