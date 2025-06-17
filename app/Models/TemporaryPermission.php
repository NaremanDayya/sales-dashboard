<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class TemporaryPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'permissible_id',
        'permissible_type',
        'field',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    // 🔗 Sales rep relation
    public function salessRep()
    {
        return $this->belongsTo(SalesRep::class ,'sales_rep_id');
    }

    // 🔁 Morph to Client or Agreement
    public function permissible()
    {
        return $this->morphTo();
    }

    // ⏰ Check if permission is still valid
    public function isActive()
    {
        return !$this->used && (
            is_null($this->expires_at) || $this->expires_at->isFuture()
        );
    }
}

