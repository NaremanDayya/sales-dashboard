<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\TargetService;

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

    /**
     * Real achieved amount for a service/year from signed agreements only -
     * excludes any bank-auto-filled amount, unlike yearAchievedAmountValue()
     * which sums achieved_amount (real + bank-filled together).
     */
    public function realYearAchievement(Service $service, SalesRep $salesRep, int $year): float
    {
        $column = $service->is_flat_price ? 'price' : 'product_quantity';

        return (float) Agreement::where('sales_rep_id', $salesRep->id)
            ->where('service_id', $service->id)
            ->whereYear('signing_date', $year)
            ->sum($column);
    }

    /**
     * Net balance carried into $year from the end of the previous year
     * (positive = banked surplus, negative = still-owed shortfall) - a
     * purely informational historical fact, not netted against this year's
     * own bonus (see bonusOfYear()) until a future settlement/release
     * feature formally reconciles years against each other.
     */
    public function carriedOverFromLastYear(Service $service, SalesRep $salesRep, int $year, TargetService $targetService): float
    {
        $startDate = $salesRep->start_work_date;
        if (!$startDate) {
            return 0;
        }

        $firstEligibleMonth = $startDate->copy()->startOfMonth()->addMonth();
        $lastYearEnd = Carbon::create($year - 1, 12, 1);

        if ($lastYearEnd->lt($firstEligibleMonth)) {
            return 0;
        }

        $lastYearTarget = $targetService->getOrCreateTarget($salesRep->id, $service->id, $lastYearEnd);

        if (!$lastYearTarget) {
            return 0;
        }

        return (float) $lastYearTarget->surplus_carried_amount - (float) $lastYearTarget->carried_over_amount;
    }

    /**
     * This year's own cumulative base target (eligible months x base target)
     * through the last elapsed month - independent of any carry-over from
     * prior years or prior months. 0 if the rep isn't eligible for any
     * month of this year yet.
     */
    public function ownYearTargetToDate(Service $service, SalesRep $salesRep, int $year): float
    {
        $startDate = $salesRep->start_work_date;
        if (!$startDate) {
            return 0;
        }

        $firstEligibleMonth = $startDate->copy()->startOfMonth()->addMonth();
        $rangeStart = Carbon::create($year, 1, 1)->max($firstEligibleMonth);
        $rangeEnd = Carbon::create($year, 12, 1)->min(now()->copy()->startOfMonth());

        if ($rangeStart->gt($rangeEnd)) {
            return 0;
        }

        $monthsEligible = $rangeStart->diffInMonths($rangeEnd) + 1;

        return $service->target_amount * $monthsEligible;
    }

    /**
     * This year's own bonus: real achievement beyond what this year's own
     * (eligible months x base target) required, evaluated independently of
     * any prior year - see carriedOverFromLastYear()'s docblock. Never
     * negative; a shortfall isn't a "bonus", it's just not one.
     */
    public function bonusOfYear(Service $service, SalesRep $salesRep, int $year, float $realYearAchievement): float
    {
        return max(0, $realYearAchievement - $this->ownYearTargetToDate($service, $salesRep, $year));
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
