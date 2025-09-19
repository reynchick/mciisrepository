<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class Faculty extends Model
{
    /** @use HasFactory<\Database\Factories\FacultyFactory> */
    use HasFactory, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'faculty_id',
        'first_name',
        'middle_name',
        'last_name',
        'position',
        'designation',
        'email',
        'orcid',
        'contact_number',
        'educational_attainment',
        'field_of_specialization',
        'research_interest',
    ];

    /**
     * Scope a query to search faculty by name or ID.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('faculty_id', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Get the researches that this faculty has advised.
     */
    public function advisedResearches(): HasMany
    {
        return $this->hasMany(Research::class, 'research_adviser');
    }

     /**
     * The research panels that the faculty member belongs to.
     */
    public function panelResearch(): BelongsToMany
    {
        return $this->belongsToMany(Research::class, 'panels')
                    ->withTimestamps();
    }

    /**
     * Scope a query to find faculty members who are panelists for a specific research.
     */
    public function scopePanelistsFor($query, $researchId)
    {
        return $query->whereHas('panelResearch', function ($q) use ($researchId) {
            $q->where('research.id', $researchId);
        });
    }

    /**
     * Check if the faculty member is a panelist for a specific research.
     */
    public function isPanelistFor(Research $research): bool
    {
        return $this->panelResearch()->where('research.id', $research->id)->exists();
    }

    /**
     * Get the full name of the faculty member.
     */
    public function getFullNameAttribute(): string
    {
        if ($this->middle_name) {
            return "{$this->first_name} {$this->middle_name} {$this->last_name}";
        }
        
        return "{$this->first_name} {$this->last_name}";
    }
}
