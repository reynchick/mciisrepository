<?php

namespace App\Http\Controllers;

use App\Models\SRIG;
use App\Http\Requests\StoreSRIGRequest;
use App\Http\Requests\UpdateSRIGRequest;

class SRIGController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $srigs = SRIG::all();
        return view('srigs.index', compact('srigs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('srigs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSRIGRequest $request)
    {
        $srig = SRIG::create($request->validated());
        return redirect()->route('srigs.index')
            ->with('success', 'SRIG created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SRIG $srig)
    {
        return view('srigs.show', compact('srig'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SRIG $srig)
    {
        return view('srigs.edit', compact('srig'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSRIGRequest $request, SRIG $srig)
    {
        $srig->update($request->validated());
        return redirect()->route('srigs.index')
            ->with('success', 'SRIG updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SRIG $srig)
    {
        $srig->delete();
        return redirect()->route('srigs.index')
            ->with('success', 'SRIG deleted successfully.');
    }
}
