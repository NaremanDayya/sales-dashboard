<?php

namespace App\Console\Commands;

use App\Models\Agreement;
use App\Models\Commission;
use App\Models\SalesRep;
use App\Models\Service;
use App\Models\Target;
use App\Services\TargetService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class RecalculateTargets extends Command
{
    /**
     * Wipes existing Target/Commission rows for the selected reps/services and
     * rebuilds them from scratch using the current carry-over logic (training
     * month excluded, shortfall carries forward, over-achievement is banked and
     * auto-applied to future months) - by replaying every real agreement, in
     * signing-date order, through TargetService.
     */
    protected $signature = 'targets:recalculate
        {--sales-rep= : Only rebuild this sales rep (by id)}
        {--service= : Only rebuild this service (by id)}
        {--dry-run : Show what would be rebuilt without changing anything}
        {--force : Skip the confirmation prompt}';

    protected $description = 'Rebuild Target/Commission history from scratch using the current carry-over logic';

    public function handle(TargetService $targetService)
    {
        $dryRun = (bool) $this->option('dry-run');

        $reps = SalesRep::query()
            ->when($this->option('sales-rep'), fn ($q, $id) => $q->where('id', $id))
            ->get();

        $services = Service::query()
            ->when($this->option('service'), fn ($q, $id) => $q->where('id', $id))
            ->get();

        if ($reps->isEmpty() || $services->isEmpty()) {
            $this->error('No matching sales reps or services found.');
            return self::FAILURE;
        }

        $this->info("About to rebuild target history for {$reps->count()} sales rep(s) x {$services->count()} service(s).");

        if (!$dryRun && !$this->option('force') && !$this->confirm('This deletes existing Target/Commission rows for the selected scope and rebuilds them. Continue?')) {
            $this->warn('Aborted.');
            return self::SUCCESS;
        }

        // Nothing new is actually happening here, just correcting past bookkeeping -
        // don't re-fire "target achieved" notifications for every historical month.
        Notification::fake();

        foreach ($reps as $rep) {
            if (!$rep->start_work_date) {
                $this->line("⏩ Skipping rep {$rep->id} ({$rep->name}): no start_work_date on file");
                continue;
            }

            foreach ($services as $service) {
                $agreements = Agreement::with(['service', 'salesRep.user'])
                    ->where('sales_rep_id', $rep->id)
                    ->where('service_id', $service->id)
                    ->orderBy('signing_date')
                    ->get();

                if ($dryRun) {
                    $this->line("Would rebuild rep {$rep->id} ({$rep->name}), service {$service->id} ({$service->name}): {$agreements->count()} agreement(s)");
                    continue;
                }

                DB::transaction(function () use ($rep, $service, $agreements, $targetService) {
                    Commission::where('sales_rep_id', $rep->id)->where('service_id', $service->id)->delete();
                    Target::where('sales_rep_id', $rep->id)->where('service_id', $service->id)->delete();

                    foreach ($agreements as $agreement) {
                        $targetService->handleAgreement($agreement);
                    }

                    // Backfill pure carry-over (no new achievement) up to the current
                    // month, in case the most recent agreement isn't from this month.
                    $targetService->getOrCreateTarget($rep->id, $service->id, now());
                });

                $this->info("✅ Rebuilt rep {$rep->id} ({$rep->name}), service {$service->id} ({$service->name}): {$agreements->count()} agreement(s) replayed");
            }
        }

        $this->info($dryRun ? 'Dry run complete - nothing was changed.' : 'Done.');
        return self::SUCCESS;
    }
}
