<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Target;
use App\Models\Commission;

class BackfillTargetCommissions extends Command
{
protected $signature = 'targets:backfill-commissions';
    protected $description = 'Backfill commission_id in targets table based on target_id in commissions table';

    public function handle()
    {
        $this->info('Starting backfill...');

        $updatedCount = 0;

        Target::chunk(100, function ($targets) use (&$updatedCount) {
            foreach ($targets as $target) {
                $commission = Commission::where('target_id', $target->id)->first();
                if ($commission) {
                    $target->commission_id = $commission->id;
                    $target->save();
                    $updatedCount++;
                }
            }
        });

        $this->info("Backfill complete. {$updatedCount} targets updated.");
        return Command::SUCCESS;
    }
}

