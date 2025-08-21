<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable =
        [
            'name',
            'description',
            'target_amount',
            'is_flat_price',
	    'commission_rate',
        ];
    protected $casts = [
        'is_flat_price' => 'boolean',
    ];
    public function targets()
    {
        return $this->hasMany(Target::class);
    }
    public function agreements()
    {
        return $this->hasMany(Agreement::class);
    }
    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }
    public function getIsFlatPriceLabelAttribute()
{
    return $this->is_flat_price ? 'Flat Price' : 'Per Quantity';
}
public function inActiveAgreements()
{
    return $this->agreements()->where('agreement_status', 'expired');
}

public function activeAgreements()
{
    return $this->agreements()->where('agreement_status', 'active');
}


}
