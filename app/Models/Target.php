<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_rep_id',
        'service_id',
        'month',
        'year',
        'target_amount',
        'achieved_amount',
        'is_achieved',
        'commission_due',
        'carried_over_amount',
        'achieved_percentage',
	'needed_achieved_percentage',
    ];

    protected $casts = [
        'is_achieved' => 'boolean',
        'commission_due' => 'boolean',
    ];

    public function salesRep()
    {
        return $this->belongsTo(SalesRep::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function currentMonthAchievedAmount(Service $service, SalesRep $salesRep)
    {
        $now = now();

        return self::where('sales_rep_id', $salesRep->id)
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->where('service_id', $service->id)
            ->value('achieved_amount') ?? 0;
    }
    public function monthAchievedAmount($month, Service $service, SalesRep $salesRep)
    {
        return $this->where('sales_rep_id', $salesRep->id)
            ->where('month', $month)
            ->where('year', now()->year)
            ->where('service_id', $service->id)
            ->sum('achieved_percentage');
    }
    public function yearAchievedAmount(Service $service, SalesRep $salesRep): int
    {
        return $this->where('sales_rep_id', $salesRep->id)
            ->where('service_id', $service->id)
            ->where('year', now()->year)
            ->sum('achieved_percentage') / 12;
    }
    public function getCommissionStatusAttribute()
    {
        return $this->commission_due ? 'تصرف' : 'لا تصرف';
    }
public function commission() {
    return $this->belongsTo(Commission::class); 
}
public function getCommissionStatusByMonth($month)
{
    // First check loaded commissions
    if ($this->relationLoaded('commissions')) {
        $commission = $this->commissions
            ->where('month', $month)
            ->where('year', $this->year ?? now()->year)
            ->first();
            
        return $commission?->payment_status ?? 0;
    }

    // Fallback to query if not loaded
    return $this->commissions()
        ->where('month', $month)
        ->where('year', $this->year ?? now()->year)
        ->value('payment_status') ?? 0;
}

 public function getCommissionValueByMonth($month)
    {
    return $this->commissions()
    ->where('month', $month)
    ->where('year', $this->year ?? now()->year)
    ->first()?->commission_amount ?? 0;
    }

}
