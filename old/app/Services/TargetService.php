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
            $agreement->implementation_date,
            $achievedValue
        );
    }

    protected function updateTarget(
        int $salesRepId,
        int $serviceId,
        Carbon $date,
        float $achievedValue
    ): Target {
//dd('test');       
 return DB::transaction(function () use ($salesRepId, $serviceId, $date, $achievedValue) {
            $service = Service::findOrFail($serviceId);
            $baseTarget = $service->target_amount;

            // Get or create target
            $target = Target::firstOrCreate(
                [
                    'sales_rep_id' => $salesRepId,
                    'service_id' => $serviceId,
                    'month' => $date->month,
                    'year' => $date->year,
                ],
                [
                    'target_amount' => $baseTarget,
                    'achieved_amount' => 0,
                    'carried_over_amount' => 0,
                ]
            );

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
            ->whereMonth('implementation_date', $month)
            ->whereYear('implementation_date', $year)
        ->sum('total_amount');
        // dd($agreements);

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
