<?php

namespace App\Services;

use App\Models\Agreement;
use App\Models\Commission;
use App\Models\SalesRep;
use App\Models\Service;
use App\Models\User;
use App\Notifications\TargetAchievedNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Target;
use App\Models\Setting;
class TargetService
{
    public function handleAgreement(Agreement $agreement): void
    {
        $service = $agreement->service;
        $achievedValue = $service->is_flat_price
            ? $agreement->price
            : $agreement->product_quantity;

        $this->updateTarget(
            $agreement->sales_rep_id,
            $agreement->service_id,
            $agreement->signing_date,
            $achievedValue
        );
    }

    /**
     * Get the target row for a given sales rep/service/month, creating it (and any
     * missing months before it, back to the rep's first eligible month) if needed
     * so the unmet portion of every prior month's target compounds into this one.
     *
     * The rep's joining month itself is a training month and never has a target
     * (returns null if asked for it, or any month before it) - the target clock
     * starts the month after they joined.
     */
    public function getOrCreateTarget(int $salesRepId, int $serviceId, Carbon $date): ?Target
    {
        $month = $date->month;
        $year = $date->year;

        $existing = Target::where([
            'sales_rep_id' => $salesRepId,
            'service_id' => $serviceId,
            'month' => $month,
            'year' => $year,
        ])->first();

        if ($existing) {
            return $existing;
        }

        $service = Service::findOrFail($serviceId);
        $salesRep = SalesRep::findOrFail($salesRepId);

        $targetMonth = Carbon::create($year, $month, 1)->startOfMonth();

        $carriedIn = 0;
        if ($salesRep->start_work_date) {
            $joinMonth = $salesRep->start_work_date->copy()->startOfMonth();
            $firstEligibleMonth = $joinMonth->copy()->addMonth();

            // The joining month (training) never gets a target.
            if ($targetMonth->lt($firstEligibleMonth)) {
                return null;
            }

            if ($targetMonth->gt($firstEligibleMonth)) {
                $previousMonth = $targetMonth->copy()->subMonth();
                $previousTarget = $this->getOrCreateTarget($salesRepId, $serviceId, $previousMonth);
                $carriedIn = $previousTarget?->carried_over_amount ?? 0;
            }
        }

        $actualTarget = $service->target_amount + $carriedIn;

        return Target::create([
            'sales_rep_id' => $salesRepId,
            'service_id' => $serviceId,
            'month' => $month,
            'year' => $year,
            'target_amount' => $actualTarget,
            'achieved_amount' => 0,
            'carried_over_amount' => $actualTarget,
            'achieved_percentage' => 0,
            'is_achieved' => false,
            'commission_due' => false,
            'needed_achieved_percentage' => Setting::where('key', 'commission_threshold')->value('value') ?? 90,
        ]);
    }

    protected function updateTarget(
        int $salesRepId,
        int $serviceId,
        Carbon $date,
        float $achievedValue
    ): ?Target {
 return DB::transaction(function () use ($salesRepId, $serviceId, $date, $achievedValue) {
            $target = $this->getOrCreateTarget($salesRepId, $serviceId, $date);

            if (!$target) {
                // Signed during the rep's training month - doesn't count toward any target.
                return null;
            }

            // Update achieved amount
            $target->achieved_amount += $achievedValue;

            // Calculate carryover for NEXT month
            $target->carried_over_amount = max(0, $target->target_amount - $target->achieved_amount);
            //calculate achieved_percentage
            $target->achieved_percentage = ($target->achieved_amount / $target->target_amount) * 100;
            // Check achievement (90% of target_amount)
$threshold =Setting::where('key', 'commission_threshold')->value('value') ?? 90 ;
if ($target->achieved_percentage >= $threshold){
    $target->is_achieved = 1;
    $target->commission_due = 1;

    $achievedTotalAmount = $this->calculateAchievedTotalAmount(
        $salesRepId, $serviceId, $target->month, $target->year
    );

    $target->salesRep->user->notify(new TargetAchievedNotification($target));

$commissionAmount = 0;
$commissionRate = 0;

if (!$target->service->is_flat_price) {
    // الحالة المخصصة، العمولة ستكون مبدئياً صفر حتى يحددها الإدمن
    $commissionRate = 0;
    $commissionAmount = 0;
} else {
    $commissionRate = $target->service->commission_rate;
    $commissionAmount = ($commissionRate / 100) * $achievedTotalAmount;
}

$commission = $this->createOrUpdateCommission(
    $target,
    $salesRepId,
    $serviceId,
    $target->month,
    $target->year,
    $commissionAmount,
    $achievedTotalAmount,
    $commissionRate,
);

    if ($commission) {
        $target->commission_id = $commission->id;
    }
}

$target->save(); 
            return $target;

        });
    }

    protected function calculateAchievedTotalAmount(
        int $salesRepId,
        int $serviceId,
        int $month,
        int $year
    ): float {
        return Agreement::query()
            ->where('sales_rep_id', $salesRepId)
            ->where('service_id', $serviceId)
            ->whereMonth('signing_date', $month)
            ->whereYear('signing_date', $year)
        ->sum('total_amount');
    }

protected function createOrUpdateCommission(
    Target $target,
    int $salesRepId,
    int $serviceId,
    int $month,
    int $year,
    float $commissionAmount,
    float $achievedTotalAmount,
    float $commissionRate,
): Commission {
    $commission = Commission::updateOrCreate(
        [
            'sales_rep_id' => $salesRepId,
            'service_id' => $serviceId,
            'year' => $year,
            'month' => $month,
        ],
        [
            'target_id' => $target->id,
            'commission_amount' => $commissionAmount,
            'total_achieved_amount' => $achievedTotalAmount,
            'achieved_percentage' => $target->achieved_percentage,
            'commission_rate' => $commissionRate,
        ]
    );

    if (!$target->commission_due && $commissionAmount > 0) {
        $target->update(['commission_due' => true]);
    }

    return $commission;
}


}
