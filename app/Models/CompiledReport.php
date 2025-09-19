<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompiledReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type_id',
        'report_format_id',
        'generated_by',
        'generated_on',
        'filters_applied',
        'file_path',
    ];

    protected $casts = [
        'filters_applied' => 'array',
        'generated_on' => 'datetime',
    ];

    public function reportType(): BelongsTo
    {
        return $this->belongsTo(ReportType::class);
    }

    public function reportFormat(): BelongsTo
    {
        return $this->belongsTo(ReportFormat::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}