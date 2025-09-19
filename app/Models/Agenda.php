<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Agenda extends Model
{
    /** @use HasFactory<\Database\Factories\AgendaFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function researches(): BelongsToMany
    {
        return $this->belongsToMany(Research::class, 'research_agenda', 'agenda_id', 'research_id')
            ->withTimestamps();
    }
}