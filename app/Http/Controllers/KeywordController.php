<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Http\Requests\StoreKeywordRequest;
use App\Http\Requests\UpdateKeywordRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KeywordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Keyword::query()
            ->withCount(['researches', 'researches as active_research_count' => function ($query) {
                $query->whereNull('archived_at');
            }])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search($request->search);
            });

        $keywords = $query->paginate(20);

        return Inertia::render('Keyword/Index', [
            'keywords' => $keywords,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Keyword/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKeywordRequest $request)
    {
        Keyword::create($request->validated());

        return redirect()->route('keywords.index')
            ->with('success', 'Keyword created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Keyword $keyword): Response
    {
        $keyword->load(['researches' => function ($query) {
            $query->with(['researchers', 'program'])->latest();
        }]);

        return Inertia::render('Keyword/Show', [
            'keyword' => $keyword
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Keyword $keyword): Response
    {
        return Inertia::render('Keyword/Edit', [
            'keyword' => $keyword
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKeywordRequest $request, Keyword $keyword)
    {
        $keyword->update($request->validated());

        return redirect()->route('keywords.show', $keyword)
            ->with('success', 'Keyword updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Keyword $keyword)
    {
        $keyword->delete();

        return redirect()->route('keywords.index')
            ->with('success', 'Keyword deleted successfully.');
    }

    /**
     * Display the statistics of the keywords.
     */
    public function statistics()
    {
        $stats = [
            'total_keywords' => Keyword::count(),
            'most_used' => Keyword::withCount('researches')
                ->orderByDesc('researches_count')
                ->limit(10)
                ->get(),
            'by_year' => Keyword::with(['researches' => function ($query) {
                $query->select('published_year')
                    ->groupBy('published_year');
            }])->get()
        ];

        return $this->success('Statistics retrieved successfully', $stats);
    }
}
