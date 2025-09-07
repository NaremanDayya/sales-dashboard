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

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'target_id');
    }
    public function monthAchievedAmount($month, Service $service, SalesRep $salesRep)
    {
        return $this->where('sales_rep_id', $salesRep->id)
            ->where('month', $month)
            ->where('year', now()->year)
            ->where('service_id', $service->id)
            ->sum('achieved_percentage');
    }

    public function yearAchievedAmountValue(Service $service, SalesRep $salesRep): float
    {
        $currentYear = now()->year;

        $totalAchievedAmount = $this->where('sales_rep_id', $salesRep->id)
            ->where('service_id', $service->id)
            ->where('year', $currentYear)
            ->sum('achieved_amount');

        return (float) $totalAchievedAmount;
    }

    public function yearAchievedAmount(Service $service, SalesRep $salesRep): float
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $startDate = optional($salesRep->start_work_date)->startOfMonth();

        if (!$startDate) {
            $monthsWorked = $currentMonth;
        } elseif ($startDate->year > $currentYear) {
            $monthsWorked = 0;
        } elseif ($startDate->year == $currentYear) {
            $startMonth = $startDate->month + 1;
            $monthsWorked = 12 - $startMonth + 1;
            $monthsWorked = max(0, $monthsWorked);
        } else {
            $monthsWorked = $currentMonth;
        }

        if ($monthsWorked === 0) {
            return 0.0;
        }

        $totalAchievedAmount = $this->where('sales_rep_id', $salesRep->id)
            ->where('service_id', $service->id)
            ->where('year', $currentYear)
            ->sum('achieved_amount');

        $targetAmount = ($service->target_amount) * $monthsWorked ?: 1;

        $yearAchieved = $totalAchievedAmount / $targetAmount *100;
//        dd($totalAchievedAmount ,$targetAmount,$yearAchieved);

        return (int) $yearAchieved;
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
