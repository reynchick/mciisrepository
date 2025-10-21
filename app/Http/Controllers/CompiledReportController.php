<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompiledReportRequest;
use App\Models\CompiledReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompiledReportController extends Controller
{
    /**
     * Initialize controller and authorize resource actions.
     */
    public function __construct()
    {
        $this->authorizeResource(CompiledReport::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $reports = CompiledReport::with(['reportType', 'reportFormat', 'generatedBy'])
            ->latest()
            ->paginate();

        return response()->json($reports);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompiledReportRequest $request): JsonResponse
    {
        $report = CompiledReport::create([
            ...$request->validated(),
            'generated_by' => Auth::id(),
            'generated_on' => now(),
        ]);

        return response()->json($report, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CompiledReport $compiledReport): JsonResponse
    {
        return response()->json($compiledReport->load([
            'reportType',
            'reportFormat',
            'generatedBy'
        ]));
    }

    /**
     * Download the compiled report file.
     */
    public function download(CompiledReport $compiledReport): BinaryFileResponse
    {
        $this->authorize('download', $compiledReport);

        return response()->download(
            Storage::path($compiledReport->file_path)
        );
    }
}