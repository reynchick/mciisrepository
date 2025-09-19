<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class Audit extends Model
{
    protected $fillable = [
        'auditable_type', 
        'auditable_id',
        'user_id', 
        'event',
        'old_values',
        'new_values', 
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'metadata' => 'json'
    ];

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by event type
     */
    public function scopeEvent(Builder $query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    /**
     * Scope to filter by auditable type
     */
    public function scopeAuditableType(Builder $query, string $type): Builder
    {
        return $query->where('auditable_type', $type);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Get human-readable event description
     */
    public function getEventDescriptionAttribute(): string
    {
        return match($this->event) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored',
            'attached' => 'Attached',
            'detached' => 'Detached',
            'synced' => 'Synced',
            default => ucfirst($this->event)
        };
    }

    /**
     * Get the auditable model name (without namespace)
     */
    public function getAuditableModelNameAttribute(): string
    {
        return class_basename($this->auditable_type);
    }
}