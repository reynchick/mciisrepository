<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the users that belong to this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope a query to search roles by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }
    

    /**
     * Check if this role is for administrators.
     */
    public function isAdministrator(): bool
    {
        return $this->name === 'Administrator';
    }

    /**
     * Check if this role is for faculty.
     */
    public function isFaculty(): bool
    {
        return $this->name === 'Faculty';
    }

    /**
     * Check if this role is for students.
     */
    public function isStudent(): bool
    {
        return $this->name === 'Student';
    }

    /**
     * Check if this role is for MCIIS staff.
     */
    public function isMCIISStaff(): bool
    {
        return $this->name === 'MCIIS Staff';
    }
}
