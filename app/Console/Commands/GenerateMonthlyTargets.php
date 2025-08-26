<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesRep;
use App\Models\Service;
use App\Models\Target;
use Carbon\Carbon;
use App\Models\Setting;
class GenerateMonthlyTargets extends Command
{
    protected $signature = 'targets:generate-monthly';
    protected $description = 'Generate monthly targets for all sales reps and services';

    public function handle()
    {

$salesReps = SalesRep::whereHas('user', function ($query) {
    $query->where('account_status', 'active');
})->get();

$services = Service::all();

$now = Carbon::now();
$month = $now->month;
$year = $now->year;
$date = Carbon::create($year, $month, 1);

$log = [];

foreach ($salesReps as $rep) {

$trainingEnd = $rep->start_work_date->copy()->addMonth()->startOfMonth();

if ($trainingEnd->gt($date)) {
    $log[] = "⏩ Skipped Rep ID {$rep->id} for $month/$year: still in training (Started: {$rep->start_work_date->format('Y-m-d')})";
    continue;

    }

    foreach ($services as $service) {
        $existing = Target::where([
            'sales_rep_id' => $rep->id,
            'service_id' => $service->id,
            'month' => $month,
            'year' => $year,
        ])->first();

        if ($existing) {
            $log[] = "🔁 Exists: Rep {$rep->id} - Service {$service->id} - $month/$year";
            continue;
        }

        $firstEligibleMonth = $rep->start_work_date->copy()->addMonth()->startOfMonth();

        $carryoverMonths = 0;
        $missingCarryover = 0;

        $previousDate = $date->copy()->subMonthNoOverflow(); // last month
        $previousTarget = Target::where([
            'sales_rep_id' => $rep->id,
            'service_id' => $service->id,
            'month' => $previousDate->month,
            'year' => $previousDate->year,
        ])->first();

        if ($previousTarget) {
            $missingCarryover = $previousTarget->carried_over_amount;
        } else {
            $periodStart = $firstEligibleMonth->copy();
            $periodEnd   = $date->copy()->subMonthNoOverflow();
            $carryoverMonths = $periodEnd->diffInMonths($periodStart) + 1;

            if ($carryoverMonths > 0) {
                $missingCarryover = $carryoverMonths * $service->target_amount;
            }
        }

        $baseTarget = $service->target_amount;
        $actualTarget = $baseTarget + $missingCarryover;

        Target::create([
            'sales_rep_id' => $rep->id,
            'service_id' => $service->id,
            'month' => $month,
            'year' => $year,
            'target_amount' => $actualTarget,
            'achieved_amount' => 0,
            'carried_over_amount' => $actualTarget,
            'is_achieved' => false,
            'commission_due' => 0,
	    'needed_achieved_percentage' => Setting::where('key', 'commission_threshold')->value('value') ?? 90 ,
        ]);

        $log[] = "✅ Created: Rep {$rep->id}, Service {$service->id}, Month $month/$year, Target: $actualTarget (Base: $baseTarget + Carry: $previousCarryover)";
    }
}


    }
}
