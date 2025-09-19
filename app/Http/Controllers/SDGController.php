<?php

namespace App\Http\Controllers;

use App\Models\SDG;
use App\Http\Requests\StoreSDGRequest;
use App\Http\Requests\UpdateSDGRequest;
use Illuminate\Http\Request;

class SDGController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', SDG::class);

        $sdgs = SDG::query()
            ->withCount('researches')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(15);

        return response()->json($sdgs);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', SDG::class);

        return response()->noContent();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSDGRequest $request)
    {
        $this->authorize('create', SDG::class);

        SDG::create($request->validated());

        return redirect()->back()->with('success', 'SDG created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SDG $sDG)
    {
        $this->authorize('view', $sDG);

        return response()->json($sDG->load(['researches' => function ($q) {
            $q->latest();
        }]));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SDG $sDG)
    {
        $this->authorize('update', $sDG);

        return response()->json($sDG);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSDGRequest $request, SDG $sDG)
    {
        $this->authorize('update', $sDG);

        $sDG->update($request->validated());

        return redirect()->back()->with('success', 'SDG updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SDG $sDG)
    {
        $this->authorize('delete', $sDG);

        $sDG->delete();

        return redirect()->back()->with('success', 'SDG deleted successfully.');
    }
}