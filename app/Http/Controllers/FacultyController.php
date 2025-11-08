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
        // Check for advised researches
        if ($faculty->advisedResearches()->count() > 0) {
            return $this->error('Cannot delete faculty member with existing research advising.');
        }
        
        // Check for paneled research
        if ($faculty->paneledResearch()->count() > 0) {
            return $this->error('Cannot delete faculty member assigned as panelist to research.');
        }
        
        // Check for linked user account
        if ($faculty->user()->exists()) {
            return $this->error('Cannot delete faculty member with linked user account.');
        }

        $faculty->delete();
        
        return redirect()->route('faculty.index')
            ->with('success', 'Faculty member deleted successfully.');
    }
}