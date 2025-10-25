<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Faculty extends Model
{

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
     * Get the user account associated with this faculty record.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'faculty_id', 'faculty_id');
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
    public function paneledResearch(): BelongsToMany
    {
        return $this->belongsToMany(Research::class, 'panels')->withTimestamps();
    }

    /**
     * Get the audit logs targeting this faculty account.
     */
    public function facultyAuditLogsTargeting(): HasMany
    {
        return $this->hasMany(FacultyAuditLog::class, 'target_faculty_id');
    }

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
        return collect([$this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->join(' ');
    }


    /**
     * Get the number of researches this faculty has advised and paneled.
     */
    public function getResearchCounts() {
        return [
            'advised' => $this->advisedResearch()->count(),
            'paneled' => $this->paneledResearch()->count()
        ];
    }
}
