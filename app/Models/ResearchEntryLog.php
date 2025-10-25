<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchEntryLog extends Model
{
    public const ACTION_CREATE = 'create_research_entry';
    public const ACTION_UPDATE = 'update_research_entry';
    public const ACTION_ARCHIVE = 'archive_research_entry';

    protected $fillable = [
        'modified_by',
        'target_research_id',
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
     * Get the research record that was modified.
     */
    public function targetResearch(): BelongsTo
    {
        return $this->belongsTo(Research::class, 'target_research_id');
    }

    public static function getActionTypes(): array
    {
        return [
            self::ACTION_CREATE => 'Create Research Entry',
            self::ACTION_UPDATE => 'Update Research Entry',
            self::ACTION_ARCHIVE => 'Archive Research Entry',
        ];
    }
}