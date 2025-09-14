<?php

namespace App\Models;

class KeywordSearchLog extends Model {
    protected $fillable = ['keyword_id', 'user_id'];
    
    public function keyword() {
        return $this->belongsTo(Keyword::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}