<?php

namespace App\Http\Controllers;

use App\Models\Researcher;
use App\Http\Requests\StoreResearcherRequest;
use App\Http\Requests\UpdateResearcherRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResearcherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Researcher::with(['research' => function ($query) {
                $query->with('program');
            }])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search($request->search);
            })
            ->when($request->filled('research'), function ($query) use ($request) {
                $query->byResearch($request->research);
            });

        $researchers = $query->paginate(15);

        return Inertia::render('Researcher/Index', [
            'researchers' => $researchers,
            'filters' => $request->only(['search', 'research'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Researcher/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResearcherRequest $request)
    {
        $researcher = Researcher::create($request->validated());

        return redirect()->route('researchers.show', $researcher)
            ->with('success', 'Researcher created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Researcher $researcher): Response
    {
        $researcher->load(['research' => function ($query) {
            $query->with(['program', 'keywords']);
        }]);

        return Inertia::render('Researcher/Show', [
            'researcher' => $researcher
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Researcher $researcher): Response
    {
        $researcher->load('research');

        return Inertia::render('Researcher/Edit', [
            'researcher' => $researcher
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResearcherRequest $request, Researcher $researcher)
    {
        $researcher->update($request->validated());

        return redirect()->route('researchers.show', $researcher)
            ->with('success', 'Researcher updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Researcher $researcher)
    {
        $researcher->delete();

        return redirect()->route('researchers.index')
            ->with('success', 'Researcher deleted successfully.');
    }
}
