<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesRep;
use App\Models\Service;
use App\Services\TargetService;
use Carbon\Carbon;

class GenerateMonthlyTargets extends Command
{
    protected $signature = 'targets:generate-monthly';
    protected $description = 'Generate monthly targets for all sales reps and services';

    public function handle(TargetService $targetService)
    {
        $salesReps = SalesRep::whereHas('user', function ($query) {
            $query->where('account_status', 'active');
        })->get();

        $services = Service::all();

        $now = Carbon::now()->startOfMonth();

        foreach ($salesReps as $rep) {
            if (!$rep->start_work_date) {
                $this->line("⏩ Skipped Rep ID {$rep->id}: no start_work_date on file");
                continue;
            }

            // A rep's target starts the month after they joined; nothing to
            // generate before that (their joining month itself is training).
            if ($rep->start_work_date->copy()->startOfMonth()->gt($now)) {
                $this->line("⏩ Skipped Rep ID {$rep->id}: joins in the future ({$rep->start_work_date->format('Y-m-d')})");
                continue;
            }

            foreach ($services as $service) {
                // getOrCreateTarget backfills every eligible month between the rep's
                // first target month and now, compounding each month's shortfall
                // into the next, then returns (or creates) this month's row. Returns
                // null if this month is still the rep's training month.
                $target = $targetService->getOrCreateTarget($rep->id, $service->id, $now);

                if (!$target) {
                    $this->line("⏩ Rep {$rep->id}, Service {$service->id}: still in training this month ({$now->month}/{$now->year})");
                    continue;
                }

                $this->line("✅ Rep {$rep->id}, Service {$service->id}, {$now->month}/{$now->year}, Target: {$target->target_amount}, CarryOver: {$target->carried_over_amount}");
            }
        }
    }
}
