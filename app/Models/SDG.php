<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SDG extends Model
{
    /** @use HasFactory<\Database\Factories\SDGFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function researches(): BelongsToMany
    {
        return $this->belongsToMany(Research::class, 'research_sdg')->withTimestamps();
    }
}