<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompiledReportRequest;
use App\Models\CompiledReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompiledReportController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CompiledReport::class);
    }

    public function index(): JsonResponse
    {
        $reports = CompiledReport::with(['reportType', 'reportFormat', 'generatedBy'])
            ->latest()
            ->paginate();

        return response()->json($reports);
    }

    public function store(StoreCompiledReportRequest $request): JsonResponse
    {
        $report = CompiledReport::create([
            ...$request->validated(),
            'generated_by' => auth()->id(),
            'generated_on' => now(),
        ]);

        return response()->json($report, 201);
    }

    public function show(CompiledReport $compiledReport): JsonResponse
    {
        return response()->json($compiledReport->load([
            'reportType',
            'reportFormat',
            'generatedBy'
        ]));
    }

    public function download(CompiledReport $compiledReport): BinaryFileResponse
    {
        $this->authorize('download', $compiledReport);

        return response()->download(
            Storage::path($compiledReport->file_path)
        );
    }
}