<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Http\Requests\StoreAgendaRequest;
use App\Http\Requests\UpdateAgendaRequest;

class AgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Agenda::class);

        $agendas = Agenda::query()->latest()->paginate(15);

        return response()->json($agendas);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Agenda::class);

        return response()->noContent();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAgendaRequest $request)
    {
        $this->authorize('create', Agenda::class);

        $agenda = Agenda::create($request->validated());

        return redirect()->back()->with('success', 'Agenda created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Agenda $agenda)
    {
        $this->authorize('view', $agenda);

        return response()->json($agenda->load('researches'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agenda $agenda)
    {
        $this->authorize('update', $agenda);

        return response()->json($agenda);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAgendaRequest $request, Agenda $agenda)
    {
        $this->authorize('update', $agenda);

        $agenda->update($request->validated());

        return redirect()->back()->with('success', 'Agenda updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agenda $agenda)
    {
        $this->authorize('delete', $agenda);

        $agenda->delete();

        return redirect()->back()->with('success', 'Agenda deleted successfully.');
    }
}