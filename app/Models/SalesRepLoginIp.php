<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Torann\GeoIP\GeoIP;
class SalesRepLoginIp extends Model
{
    use HasFactory;

    protected $table = 'sales_reps_login_ips';

    protected $fillable = [
        'sales_rep_id',
        'ip_address',
        'is_allowed',
        'is_temporary',
        'blocked_at',
	'allowed_until',
        'location',
    ];

    protected $casts = [
        'is_allowed' => 'boolean',
        'is_temporary' => 'boolean',
        'blocked_at' => 'datetime',
	'allowed_until' => 'datetime',
    ];

    // Relationships
    public function salesRep()
    {
        return $this->belongsTo(SalesRep::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('blocked_at');
    }

    public function scopePermanent($query)
    {
        return $query->where('is_temporary', false);
    }

    public function scopeTemporary($query)
    {
        return $query->where('is_temporary', true);
    }

    public function scopeValidTemporary($query)
    {
        return $query->temporary()
                     ->where('allowed_until', '>=', now())
                     ->whereNull('blocked_at');
    }

    public function getIsBlockedAttribute()
    {
        return !is_null($this->blocked_at);
    }

    public function isExpiredTemporary()
    {
        return $this->is_temporary && $this->allowed_until && now()->greaterThan($this->allowed_until);
    }
}
