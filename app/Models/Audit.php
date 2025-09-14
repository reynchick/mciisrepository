<?php

namespace App\Models;

class Audit extends Model {
    protected $fillable = [
        'auditable_type', 
        'auditable_id',
        'user_id', 
        'event',
        'old_values',
        'new_values', 
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json'
    ];

    public function auditable() {
        return $this->morphTo();
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}