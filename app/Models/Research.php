<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Traits\Auditable;

class Research extends Model
{
    /** @use HasFactory<\Database\Factories\ResearchFactory> */
    use HasFactory, Auditable;

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
     * Get the panelists for this research.
     */
    public function panelists(): BelongsToMany
    {
        return $this->belongsToMany(Faculty::class, 'panels')
                    ->withTimestamps();
    }

    /**
     * Agendas associated with this research.
     */
    public function agendas(): BelongsToMany
    {
        return $this->belongsToMany(Agenda::class, 'research_agenda', 'research_id', 'agenda_id')
            ->withTimestamps();
    }

    /**
     * Get the SDGs associated with this research.
     */
    public function sdgs(): BelongsToMany
    {
        return $this->belongsToMany(SDG::class, 'research_sdg')->withTimestamps();
    }

    /**
     * Get the SRIGs associated with this research.
     */
    public function srigs(): BelongsToMany
    {
        return $this->belongsToMany(SRIG::class, 'research_srig');
    }

    /**
     * Scope a query to filter research by panelist.
     */
    public function scopeByPanelist($query, $facultyId)
    {
        return $query->whereHas('panelists', function ($q) use ($facultyId) {
            $q->where('faculty_id', $facultyId);
        });
    }

    /**
     * Check if a faculty member is a panelist of this research.
     */
    public function hasPanelist(Faculty $faculty): bool
    {
        return $this->panelists()->where('faculty_id', $faculty->id)->exists();
    }
    

    /**
     * Scope a query to only include non-archived research.
     */
    public function scopeActive($query): Builder
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope a query to only include archived research.
     */
    public function scopeArchived($query): Builder
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

    // Add unique rule for research_title
    public static function rules($research = null)
    {
        $rules = [
            'research_title' => ['required', 'string', 'max:255', $research ? "unique:research,research_title,{$research->id}" : 'unique:research,research_title'],
            'research_adviser' => 'required|exists:faculties,id',
            'program_id' => 'required|exists:programs,id',
            'published_month' => 'nullable|integer|between:1,12',
            'published_year' => 'required|integer|between:1900,' . date('Y'),
            'research_abstract' => 'required|string',
        ];

        // Files are always optional but must meet requirements when provided
        $rules['approval_sheet'] = [
            'nullable',
            'file',
            'mimes:jpg,jpeg,png',
            'max:2048', // 2MB max
        ];
        
        $rules['manuscript'] = [
            'nullable',
            'file',
            'mimes:pdf',
            'max:10240', // 10MB max
        ];

        return $rules;
    }

    // Handle file uploads
    public function uploadFiles($approvalSheet, $manuscript)
    {
        if ($approvalSheet) {
            // Delete old file if exists
            if ($this->research_approval_sheet) {
                Storage::disk('public')->delete($this->research_approval_sheet);
            }
            
            // Store new approval sheet
            $approvalSheetPath = $approvalSheet->store('research/approval_sheets', 'public');
            $this->research_approval_sheet = $approvalSheetPath;
        }

        if ($manuscript) {
            // Delete old file if exists
            if ($this->research_manuscript) {
                Storage::disk('public')->delete($this->research_manuscript);
            }
            
            // Store new manuscript
            $manuscriptPath = $manuscript->store('research/manuscripts', 'public');
            $this->research_manuscript = $manuscriptPath;
        }

        $this->save();
    }

    // Get file URLs
    public function getApprovalSheetUrl()
    {
        return $this->research_approval_sheet ? Storage::url($this->research_approval_sheet) : null;
    }

    public function getManuscriptUrl()
    {
        return $this->research_manuscript ? Storage::url($this->research_manuscript) : null;
    }

    // Clean up files when research is deleted
    protected static function boot()
    {
        parent::boot();

        static::deleting(function($research) {
            if ($research->research_approval_sheet) {
                Storage::disk('public')->delete($research->research_approval_sheet);
            }
            if ($research->research_manuscript) {
                Storage::disk('public')->delete($research->research_manuscript);
            }
        });
    }

    /**
     * Attach keywords to research with audit logging
     */
    public function attachKeywords(array $keywordIds): void
    {
        $oldKeywords = $this->keywords()->pluck('keywords.id')->toArray();
        $this->keywords()->sync($keywordIds);
        
        $newKeywords = $this->keywords()->pluck('keywords.id')->toArray();
        $added = array_diff($newKeywords, $oldKeywords);
        $removed = array_diff($oldKeywords, $newKeywords);

        if (!empty($added) || !empty($removed)) {
            $this->logAudit('synced', null, [
                'relation' => 'keywords',
                'added' => $added,
                'removed' => $removed
            ]);
        }
    }

    /**
     * Attach panelists to research with audit logging
     */
    public function attachPanelists(array $facultyIds): void
    {
        $oldPanelists = $this->panelists()->pluck('faculty.id')->toArray();
        $this->panelists()->sync($facultyIds);
        
        $newPanelists = $this->panelists()->pluck('faculty.id')->toArray();
        $added = array_diff($newPanelists, $oldPanelists);
        $removed = array_diff($oldPanelists, $newPanelists);

        if (!empty($added) || !empty($removed)) {
            $this->logAudit('synced', null, [
                'relation' => 'panelists',
                'added' => $added,
                'removed' => $removed
            ]);
        }
    }
}