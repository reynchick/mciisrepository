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
        $query = Faculty::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'last_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        $faculties = $query->paginate(15);
        
        return Inertia::render('Faculty/Index', [
            'faculties' => $faculties,
            'filters' => $request->only(['search', 'sort_by', 'sort_order']),
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
        
        return redirect()->route('faculties.index')
            ->with('success', 'Faculty member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty): Response
    {
        return Inertia::render('Faculty/Show', [
            'faculty' => $faculty,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faculty $faculty): Response
    {
        return Inertia::render('Faculty/Edit', [
            'faculty' => $faculty,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        $faculty->update($request->validated());
        
        return redirect()->route('faculties.show', $faculty)
            ->with('success', 'Faculty member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty)
    {
        $faculty->delete();
        
        return redirect()->route('faculties.index')
            ->with('success', 'Faculty member deleted successfully.');
    }

    /**
     * Bulk operations
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'faculty_ids' => 'required|array',
            'faculty_ids.*' => 'exists:faculties,id'
        ]);
        
        Faculty::whereIn('id', $request->faculty_ids)->delete();
        
        return redirect()->route('faculties.index')
            ->with('success', 'Selected faculty members deleted successfully.');
    }

    /**
     * Export faculty data
     */
    public function export(Request $request)
    {
        $faculties = Faculty::all();
        
        // You can implement CSV/Excel export here
        // For now, returning JSON response
        return response()->json($faculties);
    }

    /**
     * Get faculty statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Faculty::count(),
            'with_email' => Faculty::whereNotNull('email')->count(),
            'with_orcid' => Faculty::whereNotNull('orcid')->count(),
            'by_position' => Faculty::selectRaw('position, COUNT(*) as count')
                ->whereNotNull('position')
                ->groupBy('position')
                ->get(),
        ];
        
        return response()->json($stats);
    }
}