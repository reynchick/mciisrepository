<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeywordSearchLog extends Model
{

    protected $fillable = ['keyword_id', 'user_id', 'ip_address', 'user_agent'];
    
    /**
     * Get the keyword that was searched.
     */
    public function keyword(): BelongsTo
    {
        return $this->belongsTo(Keyword::class);
    }
    
    /**
     * Get the user who performed the search.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}