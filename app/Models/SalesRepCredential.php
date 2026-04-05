<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SalesRepCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_rep_id',
        'user_id',
        'name',
        'email',
        'password',
        'password_changed_at',
        'changed_by',
    ];

    protected $casts = [
        'password_changed_at' => 'datetime',
    ];

    public function salesRep()
    {
        return $this->belongsTo(SalesRep::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Set the password attribute (encrypt for storage)
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    /**
     * Get the password attribute (decrypt for display)
     */
    public function getPasswordAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Store or update credential
     */
    public static function storeCredential($salesRepId, $userId, $name, $email, $plainPassword, $changedBy = null)
    {
        return self::updateOrCreate(
            ['sales_rep_id' => $salesRepId],
            [
                'user_id' => $userId,
                'name' => $name,
                'email' => $email,
                'password' => $plainPassword,
                'password_changed_at' => now(),
                'changed_by' => $changedBy ?? auth()->id(),
            ]
        );
    }
}
