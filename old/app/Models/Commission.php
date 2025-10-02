<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_rep_id',
        'service_id',
        'target_id',
        'month',
        'year',
        'achieved_percentage',
        'total_achieved_amount',
        'commission_amount',
        'commission_rate',
	'payment_status',
	'calculation_type',
	'item_fee',
    ];
protected $casts= [
'payment_status' => 'boolean',
];
    /**
     * The sales representative who earned this commission.
     */
    public function salesRep()
    {
        return $this->belongsTo(SalesRep::class, 'sales_rep_id');
    }

    /**
     * The service for which this commission was calculated.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);

    }public function target()
    {
        return $this->belongsTo(Target::class);
    }

}

