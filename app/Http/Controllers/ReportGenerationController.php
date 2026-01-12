<?php

namespace App\Http\Controllers;

use App\Models\CompiledReport;
use App\Models\ReportType;
use App\Models\ReportFormat;
use App\Models\Research;
use App\Models\Faculty;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportGenerationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function compilation(Request $request): JsonResponse
    {
        $filters = $this->filters($request);
        $items = $this->queryResearch($filters)->with(['program', 'adviser', 'researchers', 'keywords'])->get();

        $rtf = $this->toRtfCompilation($items);
        $filename = 'compilation_' . now()->format('Ymd_His') . '.rtf';
        $path = Storage::put('compiled/' . $filename, $rtf) ? ('compiled/' . $filename) : null;

        $typeId = ReportType::where('name', 'Abstract/Executive Summary Compilation')->value('id');
        $formatId = ReportFormat::where('name', 'Word')->value('id');

        $compiled = CompiledReport::create([
            'report_type_id' => $typeId,
            'report_format_id' => $formatId,
            'generated_by' => Auth::id(),
            'generated_on' => now(),
            'filters_applied' => $filters,
            'file_path' => $path,
        ]);

        return response()->json([
            'compiledReportId' => $compiled->id,
            'fileUrl' => route('compiled-reports.download', $compiled),
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $filters = $this->filters($request);
        $format = strtolower($request->input('format', 'pdf')); // 'pdf' | 'xlsx'
        $columns = (array) $request->input('columns', []);

        $items = $this->queryResearch($filters)->with(['program', 'adviser'])->get();

        if ($format === 'xlsx') {
            $csv = $this->toCsv($items, $columns);
            $filename = 'report_' . now()->format('Ymd_His') . '.csv';
            $path = Storage::put('compiled/' . $filename, $csv) ? ('compiled/' . $filename) : null;
            $formatId = ReportFormat::where('name', 'Excel')->value('id');
        } else {
            $html = $this->toHtmlTable($items, $columns);
            $filename = 'report_' . now()->format('Ymd_His') . '.html';
            $path = Storage::put('compiled/' . $filename, $html) ? ('compiled/' . $filename) : null;
            $formatId = ReportFormat::where('name', 'PDF')->value('id'); // swap to real PDF later
        }

        $typeId = ReportType::where('name', 'Tabular Report')->value('id');

        $compiled = CompiledReport::create([
            'report_type_id' => $typeId,
            'report_format_id' => $formatId,
            'generated_by' => Auth::id(),
            'generated_on' => now(),
            'filters_applied' => $filters,
            'file_path' => $path,
        ]);

        return response()->json([
            'compiledReportId' => $compiled->id,
            'fileUrl' => route('compiled-reports.download', $compiled),
        ]);
    }

    private function filters(Request $request): array
    {
        return array_filter([
            'search' => $request->input('search'),
            'program' => $request->input('program'),
            'year' => $request->input('year'),
            'adviser' => $request->input('adviser'),
            'startDate' => $request->input('startDate'),
            'endDate' => $request->input('endDate'),
            'alignment' => $request->input('alignment'),
        ], fn($v) => $v !== null && $v !== '');
    }

    private function queryResearch(array $filters)
    {
        $q = Research::query()->with(['program', 'adviser'])->whereNull('archived_at');

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($w) use ($s) {
                $w->where('research_title', 'LIKE', "%{$s}%")
                  ->orWhereHas('keywords', fn($k) => $k->where('keyword', 'LIKE', "%{$s}%"));
            });
        }
        if (!empty($filters['year'])) {
            $q->where('published_year', $filters['year']);
        }
        if (!empty($filters['adviser'])) {
            $q->whereHas('adviser', fn($a) => $a->where('id', $filters['adviser']));
        }
        if (!empty($filters['program'])) {
            $q->whereHas('program', fn($p) => $p->where('id', $filters['program']));
        }
        if (!empty($filters['alignment'])) {
            if ($filters['alignment'] === 'sdg') $q->whereHas('sdgs');
            if ($filters['alignment'] === 'srig') $q->whereHas('srigs');
            if ($filters['alignment'] === 'agenda') $q->whereHas('agendas');
        }
        if (!empty($filters['startDate']) && !empty($filters['endDate'])) {
            $q->whereBetween('created_at', [$filters['startDate'], $filters['endDate']]);
        }

        return $q;
    }

    private function toRtfCompilation($items): string
    {
        $rtf = "{\\rtf1\\ansi\\deff0\n";
        foreach ($items as $i) {
            $title = $this->escapeRtf($i->research_title ?? '');
            $prog = $this->escapeRtf($i->program->name ?? '');
            $adv = $this->escapeRtf(($i->adviser->last_name ?? '') . ', ' . ($i->adviser->first_name ?? ''));
            $date = $this->escapeRtf(($i->published_month ?? '') . ' ' . ($i->published_year ?? ''));
            $abstract = $this->escapeRtf($i->research_abstract ?? '');
            $keywords = $this->escapeRtf(($i->keywords?->pluck('keyword')->implode(', ')) ?? '');

            $rtf .= "\\b {$title} \\b0\\line ";
            $rtf .= "{$prog} • Adviser: {$adv} • {$date}\\line ";
            $rtf .= "Keywords: {$keywords}\\line ";
            $rtf .= "{$abstract}\\par \\par ";
        }
        $rtf .= "}";
        return $rtf;
    }

    private function escapeRtf(string $s): string
    {
        return str_replace(['\\', '{', '}'], ['\\\\', '\\{', '\\}'], $s);
    }

    private function toCsv($items, array $columns): string
    {
        $cols = !empty($columns) ? $columns : ['number','title','month_year','adviser','researchers','program','agenda','srig','sdg'];
        $out = fopen('php://temp', 'w+');
        fputcsv($out, $cols);

        foreach ($items as $i) {
            $row = [];
            foreach ($cols as $c) {
                switch ($c) {
                    case 'number': $row[] = $i->id; break;
                    case 'title': $row[] = $i->research_title; break;
                    case 'month_year': $row[] = trim(($i->published_month ?? '') . ' ' . ($i->published_year ?? '')); break;
                    case 'adviser': $row[] = ($i->adviser->last_name ?? '') . ', ' . ($i->adviser->first_name ?? ''); break;
                    case 'researchers': $row[] = $i->researchers?->map(fn($r) => trim(($r->last_name ?? '') . ', ' . ($r->first_name ?? '')))->implode('; ') ?? ''; break;
                    case 'program': $row[] = $i->program->name ?? ''; break;
                    default: $row[] = ''; break;
                }
            }
            fputcsv($out, $row);
        }

        rewind($out);
        return stream_get_contents($out);
    }

    private function toHtmlTable($items, array $columns): string
    {
        $cols = !empty($columns) ? $columns : ['number','title','month_year','adviser','researchers','program'];
        $html = '<!doctype html><meta charset="utf-8"><table border="1"><thead><tr>';
        foreach ($cols as $c) $html .= '<th>'.htmlspecialchars($c).'</th>';
        $html .= '</tr></thead><tbody>';
        foreach ($items as $i) {
            $html .= '<tr>';
            foreach ($cols as $c) {
                $cell = '';
                switch ($c) {
                    case 'number': $cell = $i->id; break;
                    case 'title': $cell = $i->research_title; break;
                    case 'month_year': $cell = trim(($i->published_month ?? '').' '.($i->published_year ?? '')); break;
                    case 'adviser': $cell = ($i->adviser->last_name ?? '').', '.($i->adviser->first_name ?? ''); break;
                    case 'researchers': $cell = $i->researchers?->map(fn($r) => trim(($r->last_name ?? '').', '.($r->first_name ?? '')))->implode('; ') ?? ''; break;
                    case 'program': $cell = $i->program->name ?? ''; break;
                }
                $html .= '<td>'.htmlspecialchars($cell).'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
}