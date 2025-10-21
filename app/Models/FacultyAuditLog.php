<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacultyAuditLog extends Model
{

    protected $fillable = [
        'modified_by',
        'target_faculty_id',
        'action_type',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata' => 'array',
        ];
    }

    /**
     * Get the user who performed the modification.
     */
    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    /**
     * Get the faculty record that was modified.
     */
    public function targetFaculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'target_faculty_id');
    }
}