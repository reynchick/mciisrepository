<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\Program;
use App\Models\Faculty;
use App\Http\Requests\StoreResearchRequest;
use App\Http\Requests\UpdateResearchRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;

class ResearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Research::with(['program', 'adviser', 'researchers', 'keywords'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search($request->search);
            })
            ->when($request->filled('program'), function ($query) use ($request) {
                $query->byProgram($request->program);
            })
            ->when($request->filled('adviser'), function ($query) use ($request) {
                $query->byAdviser($request->adviser);
            })
            ->when($request->filled('year'), function ($query) use ($request) {
                $query->byYear($request->year);
            })
            ->when($request->boolean('archived'), function ($query) {
                $query->archived();
            }, function ($query) {
                $query->active();
            });

        $researches = $query->paginate(10)
            ->withQueryString();

        return Inertia::render('Research/Index', [
            'researches' => $researches,
            'programs' => Program::all(),
            'advisers' => Faculty::all(),
            'filters' => $request->only(['search', 'program', 'adviser', 'year', 'archived'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Research/Create', [
            'programs' => Program::all(),
            'advisers' => Faculty::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResearchRequest $request)
    {
        $research = Research::create($request->safe()->except(['approval_sheet', 'manuscript']));
        
        if ($request->hasFile('approval_sheet') || $request->hasFile('manuscript')) {
            $research->uploadFiles(
                $request->file('approval_sheet'),
                $request->file('manuscript')
            );
        }

        return redirect()->route('research.show', $research)
            ->with('success', 'Research created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Research $research): Response
    {
        $research->load(['program', 'adviser', 'researchers', 'keywords', 'uploader']);
        
        return Inertia::render('Research/Show', [
            'research' => $research
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Research $research): Response
    {
        $research->load(['researchers', 'keywords']);
        
        return Inertia::render('Research/Edit', [
            'research' => $research,
            'programs' => Program::all(),
            'advisers' => Faculty::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResearchRequest $request, Research $research)
    {
        $research->update($request->safe()->except(['approval_sheet', 'manuscript']));
        
        if ($request->hasFile('approval_sheet') || $request->hasFile('manuscript')) {
            $research->uploadFiles(
                $request->file('approval_sheet'),
                $request->file('manuscript')
            );
        }

        return redirect()->route('research.show', $research)
            ->with('success', 'Research updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Research $research)
    {
        $research->delete();
        
        return redirect()->route('research.index')
            ->with('success', 'Research deleted successfully.');
    }

    public function archive(Request $request, Research $research)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $research->archive(auth()->user(), $request->reason);

        return redirect()->route('research.index')
            ->with('success', 'Research archived successfully.');
    }

    public function restore(Research $research)
    {
        $research->restore();

        return redirect()->route('research.index')
            ->with('success', 'Research restored successfully.');
    }

    public function export()
    {
        $researches = Research::with(['program', 'adviser', 'researchers', 'keywords'])->get();

        return response()->streamDownload(function () use ($researches) {
            $csv = fopen('php://output', 'w');
            
            // Headers
            fputcsv($csv, [
                'Title',
                'Program',
                'Adviser',
                'Researchers',
                'Keywords',
                'Year',
                'Status'
            ]);

            // Data
            foreach ($researches as $research) {
                fputcsv($csv, [
                    $research->research_title,
                    $research->program->name,
                    $research->adviser->full_name ?? 'N/A',
                    $research->researchers->pluck('full_name')->join(', '),
                    $research->keywords->pluck('keyword_name')->join(', '),
                    $research->published_year,
                    $research->isArchived() ? 'Archived' : 'Active'
                ]);
            }

            fclose($csv);
        }, 'research-data.csv');
    }

    public function statistics()
    {
        $stats = [
            'total_research' => Research::count(),
            'active_research' => Research::active()->count(),
            'archived_research' => Research::archived()->count(),
            'by_program' => Program::withCount('researches')->get(),
            'by_year' => Research::select('published_year')
                ->get()
                ->groupBy('published_year')
                ->map->count(),
            'by_adviser' => Faculty::has('advisedResearches')
                ->withCount('advisedResearches')
                ->get()
        ];

        return $this->success('Statistics retrieved successfully', $stats);
    }

    public function downloadPdf(Research $research)
    {
        if (!$research->research_manuscript) {
            return $this->error('No manuscript file available.');
        }

        return Storage::download(
            $research->research_manuscript,
            $research->research_title . '.pdf'
        );
    }
}
