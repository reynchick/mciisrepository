<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Researcher extends Model
{

    protected $fillable = [
        'research_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
    ];

    /**
     * Get the research this researcher belongs to.
     */
    public function research(): BelongsTo
    {
        return $this->belongsTo(Research::class, 'research_id');
    }

    /**
     * Set the email attribute to lowercase.
     */
    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = $value ? strtolower($value) : null;
    }

    /**
     * Get the researcher's full name.
     */
    public function getFullNameAttribute(): string
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->join(' ');
    }

    /**
     * Scope a query to search researchers by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }
}