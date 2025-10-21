<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sdg extends Model
{
    protected $fillable = ['name', 'description'];

    /**
     * Get researches linked to this Sustainable Development Goal.
     */
    public function researches(): BelongsToMany
    {
        return $this->belongsToMany(Research::class, 'research_sdg')->withTimestamps();
    }
}