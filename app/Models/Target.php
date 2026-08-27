<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        'surplus_carried_amount',
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
    public function commissions()
    {
        return $this->hasMany(Commission::class, 'target_id');
    }

    /**
     * Total achieved amount for a service across a given year (defaults to the
     * current year), regardless of which months have target rows.
     */
    public function yearAchievedAmountValue(Service $service, SalesRep $salesRep, ?int $year = null): float
    {
        $year = $year ?? now()->year;

        $totalAchievedAmount = self::where('sales_rep_id', $salesRep->id)
            ->where('service_id', $service->id)
            ->where('year', $year)
            ->sum('achieved_amount');

        return (float) $totalAchievedAmount;
    }

    /**
     * Percentage of the year's cumulative target achieved for a service, for a
     * given year (defaults to the current year). Only months the rep was
     * actually eligible for a target (i.e. after their training month, and not
     * in the future) count toward the denominator.
     */
    public function yearAchievedAmount(Service $service, SalesRep $salesRep, ?int $year = null): int
    {
        $year = $year ?? now()->year;
        $now = now();

        $startDate = $salesRep->start_work_date;
        if (!$startDate) {
            return 0;
        }

        // The rep's first eligible target month is the month after they joined
        // (their joining month itself is training and carries no target).
        $firstEligibleMonth = $startDate->copy()->startOfMonth()->addMonth();

        $rangeStart = Carbon::create($year, 1, 1)->max($firstEligibleMonth);
        $rangeEnd = Carbon::create($year, 12, 1)->min($now->copy()->startOfMonth());

        if ($rangeStart->gt($rangeEnd)) {
            return 0;
        }

        $monthsWorked = $rangeStart->diffInMonths($rangeEnd) + 1;

        $totalAchievedAmount = self::where('sales_rep_id', $salesRep->id)
            ->where('service_id', $service->id)
            ->where('year', $year)
            ->sum('achieved_amount');

        $targetAmount = $service->target_amount * $monthsWorked;

        if ($targetAmount <= 0) {
            return 0;
        }

        return (int) round($totalAchievedAmount / $targetAmount * 100);
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
