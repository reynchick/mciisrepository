<?php

namespace App\Models;

class ResearchAccessLog extends Model {
    protected $fillable = ['research_id', 'user_id', 'ip_address', 'user_agent'];
    
    public function research() {
        return $this->belongsTo(Research::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}