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

                // Get previous month
                $previousMonth = $date->copy()->subMonth();
                $previousTarget = Target::where([
                    'sales_rep_id' => $rep->id,
                    'service_id' => $service->id,
                    'month' => $previousMonth->month,
                    'year' => $previousMonth->year,
                ])->first();

                if ($previousTarget) {
                    // If previous target exists: current target = service target + previous carried over
                    $actualTarget = $service->target_amount + $previousTarget->carried_over_amount;
                    $carriedOver = $actualTarget; // Carry over the entire amount
                } else {
                    // If no previous target: calculate months from start date (excluding first month)
                    $startDate = $rep->start_work_date->copy()->startOfMonth();
                    $firstMonthEnd = $startDate->copy()->addMonth()->startOfMonth();

                    if ($firstMonthEnd->gte($date)) {
                        // Still in first month or training period
                        $actualTarget = $service->target_amount;
                        $carriedOver = $actualTarget;
                    } else {
                        // Calculate months from start (excluding first month)
                        $monthsFromStart = $startDate->diffInMonths($date) - 1;
                        $monthsFromStart = max(0, $monthsFromStart); // Ensure non-negative

                        $carriedOver = $monthsFromStart * $service->target_amount;
                        $actualTarget = $service->target_amount + $carriedOver;
                    }
                }

                Target::create([
                    'sales_rep_id' => $rep->id,
                    'service_id' => $service->id,
                    'month' => $month,
                    'year' => $year,
                    'target_amount' => $actualTarget,
                    'achieved_amount' => 0,
                    'carried_over_amount' => $carriedOver,
                    'is_achieved' => false,
                    'commission_due' => 0,
                    'needed_achieved_percentage' => Setting::where('key', 'commission_threshold')->value('value') ?? 90,
                ]);

                $log[] = "✅ Created: SalesRep {$rep->id}, Service {$service->id}, Month $month/$year, Target: $actualTarget, CarryOver: $carriedOver";
            }
        }
    }
}
