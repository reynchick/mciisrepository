<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\Program;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Research::with(['program', 'adviser'])
            ->active(); // Only show non-archived research

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

        return Inertia::render('dashboard', [
            'researches' => $researches['data'],
            'programmes' => Program::select('id', 'name')->get(),
            'advisers' => Faculty::select('id', 'first_name', 'middle_name', 'last_name')
                ->orderBy('last_name')
                ->get(),
            'filters' => $request->only(['year', 'adviser', 'program']),
        ]);
    }
}