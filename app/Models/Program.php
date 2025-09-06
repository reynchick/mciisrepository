<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    /** @use HasFactory<\Database\Factories\ProgramFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the researches that belong to this program.
     */
    public function researches(): HasMany
    {
        return $this->hasMany(Research::class);
    }

    /**
     * Scope a query to search programs by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get the count of researches in this program.
     */
    public function getResearchCountAttribute(): int
    {
        return $this->researches()->count();
    }

    /**
     * Get the count of active researches in this program.
     */
    public function getActiveResearchCountAttribute(): int
    {
        return $this->researches()->active()->count();
    }
}
