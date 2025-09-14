<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Http\Requests\StoreFacultyRequest;
use App\Http\Requests\UpdateFacultyRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Faculty::query()
            ->withCount(['advisedResearches' => function ($query) {
                $query->whereNull('archived_at');
            }])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search($request->search);
            })
            ->when($request->filled('designation'), function ($query) use ($request) {
                $query->where('designation', $request->designation);
            });
            
        $faculties = $query->paginate(15);

        return Inertia::render('Faculty/Index', [
            'faculties' => $faculties,
            'filters' => $request->only(['search', 'designation'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Faculty/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFacultyRequest $request)
    {
        $faculty = Faculty::create($request->validated());
        
        return redirect()->route('faculty.show', $faculty)
            ->with('success', 'Faculty member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty): Response
    {
        $faculty->load(['advisedResearches' => function ($query) {
            $query->with(['program', 'researchers', 'keywords'])
                  ->latest();
        }]);

        return Inertia::render('Faculty/Show', [
            'faculty' => $faculty
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faculty $faculty): Response
    {
        return Inertia::render('Faculty/Edit', [
            'faculty' => $faculty
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        $faculty->update($request->validated());

        return redirect()->route('faculty.show', $faculty)
            ->with('success', 'Faculty member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty)
    {
        if ($faculty->advisedResearches()->count() > 0) {
            return $this->error('Cannot delete faculty member with existing research advising.');
        }

        $faculty->delete();
        
        return redirect()->route('faculty.index')
            ->with('success', 'Faculty member deleted successfully.');
    }

    /**
     * Get research statistics for faculty member
     */
    public function statistics(Faculty $faculty)
    {
        $stats = [
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
        
        return $this->success('Statistics retrieved successfully', $stats);
    }

    /**
     * Export faculty data to CSV
     */
    public function export()
    {
        $faculties = Faculty::with(['advisedResearches' => function ($query) {
            $query->with('program');
        }])->get();

        return response()->streamDownload(function () use ($faculties) {
            $csv = fopen('php://output', 'w');
            
            // Headers
            fputcsv($csv, [
                'Faculty ID',
                'Name',
                'Designation',
                'Email',
                'Research Count',
                'Active Research Count'
            ]);

            // Data
            foreach ($faculties as $faculty) {
                fputcsv($csv, [
                    $faculty->faculty_id,
                    $faculty->full_name,
                    $faculty->designation,
                    $faculty->email,
                    $faculty->advised_researches_count,
                    $faculty->advised_researches->whereNull('archived_at')->count()
                ]);
            }

            fclose($csv);
        }, 'faculty-data.csv');
    }
}