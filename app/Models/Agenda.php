<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Agenda extends Model
{
    protected $fillable = ['name', 'description'];

    /**
     * Get the researches linked to this agenda.
     */
    public function researches(): BelongsToMany
    {
        return $this->belongsToMany(Research::class, 'research_agenda')->withTimestamps();
    }
}