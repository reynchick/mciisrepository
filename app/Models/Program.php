<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the researches that belong to this program.
     */
    public function researches(): HasMany
    {
        return $this->hasMany(Research::class);
    }
}