<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Program::query()
            ->withCount(['researches', 'researches as active_research_count' => function ($query) {
                $query->whereNull('archived_at');
            }])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search($request->search);
            });

        $programs = $query->paginate(10);

        return Inertia::render('Program/Index', [
            'programs' => $programs,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program): Response
    {
        $program->load(['researches' => function ($query) {
            $query->with(['researchers', 'keywords'])
                  ->whereNull('archived_at')
                  ->latest();
        }]);

        return Inertia::render('Program/Show', [
            'program' => $program,
            'researchCount' => $program->researches_count,
            'activeResearchCount' => $program->active_research_count
        ]);
    }
}
