<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Srig extends Model
{
    protected $fillable = ['name', 'description'];

    /**
     * Get researches linked to this SRIG.
     */
    public function researches(): BelongsToMany
    {
        return $this->belongsToMany(Research::class, 'research_srig')->withTimestamps();
    }
}