<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Keyword extends Model
{
    /** @use HasFactory<\Database\Factories\KeywordFactory> */
    use HasFactory;

    protected $fillable = [
        'keyword_id',
        'keyword_name',
    ];

    /**
     * Get the researches that use this keyword.
     */
    public function researches(): BelongsToMany
    {
        return $this->belongsToMany(Research::class, 'research_keywords', 'keyword_id', 'research_id');
    }

    /**
     * Scope a query to search keywords by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('keyword_name', 'like', "%{$search}%");
    }

    /**
     * Get the count of researches using this keyword.
     */
    public function getResearchCountAttribute(): int
    {
        return $this->researches()->count();
    }

    /**
     * Get the count of active researches using this keyword.
     */
    public function getActiveResearchCountAttribute(): int
    {
        return $this->researches()->active()->count();
    }
}