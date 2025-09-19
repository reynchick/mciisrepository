<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SRIG extends Model
{
    /** @use HasFactory<\Database\Factories\SRIGFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function researches(): BelongsToMany
    {
        return $this->belongsToMany(Research::class, 'research_srig');
    }
}
