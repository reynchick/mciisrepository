<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Research extends Model
{
    /** @use HasFactory<\Database\Factories\ResearchFactory> */
    use HasFactory;

    protected $table = 'research';

    protected $fillable = [
        'uploaded_by',
        'research_title',
        'research_adviser',
        'program_id',
        'published_month',
        'published_year',
        'research_abstract',
        'research_approval_sheet',
        'research_manuscript',
        'archived_at',
        'archived_by',
        'archive_reason',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'archived_at' => 'datetime',
        'published_month' => 'integer',
        'published_year' => 'integer',
    ];

    /**
     * Get the user who uploaded this research.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the faculty adviser for this research.
     */
    public function adviser(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'research_adviser');
    }

    /**
     * Get the program this research belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /**
     * Get the user who archived this research.
     */
    public function archiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    /**
     * Get the researchers for this research.
     */
    public function researchers(): HasMany
    {
        return $this->hasMany(Researcher::class, 'research_id');
    }

    /**
     * Get the keywords associated with this research.
     */
    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class, 'research_keywords', 'research_id', 'keyword_id');
    }

    /**
     * Scope a query to only include non-archived research.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope a query to only include archived research.
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope a query to search research by title or abstract.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('research_title', 'like', "%{$search}%")
              ->orWhere('research_abstract', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by program.
     */
    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    /**
     * Scope a query to filter by adviser.
     */
    public function scopeByAdviser($query, $adviserId)
    {
        return $query->where('research_adviser', $adviserId);
    }

    /**
     * Scope a query to filter by year.
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('published_year', $year);
    }

    /**
     * Check if the research is archived.
     */
    public function isArchived(): bool
    {
        return !is_null($this->archived_at);
    }

    /**
     * Archive the research.
     */
    public function archive(User $user, string $reason = null): bool
    {
        return $this->update([
            'archived_at' => now(),
            'archived_by' => $user->id,
            'archive_reason' => $reason,
        ]);
    }

    /**
     * Restore the research from archive.
     */
    public function restore(): bool
    {
        return $this->update([
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
        ]);
    }

    /**
     * Get the full name of the research with year.
     */
    public function getFullTitleAttribute(): string
    {
        return "{$this->research_title} ({$this->published_year})";
    }

    /**
     * Get the publication date as a formatted string.
     */
    public function getPublicationDateAttribute(): string
    {
        if ($this->published_month) {
            $monthName = date('F', mktime(0, 0, 0, $this->published_month, 1));
            return "{$monthName} {$this->published_year}";
        }
        
        return (string) $this->published_year;
    }
}