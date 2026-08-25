<?php

namespace App\Console\Commands;

use App\Models\Agreement;
use App\Models\User;
use App\Notifications\AgreementRenewed;
use Illuminate\Console\Command;

class ProcessAgreementEndOfTerm extends Command
{
    protected $signature = 'agreements:process-end-of-term';
    protected $description = 'Auto-renew or expire agreements whose term has ended, based on whether a cancellation notice was received in time';

    public function handle(): void
    {
        $dueAgreements = Agreement::where('agreement_status', 'active')
            ->whereDate('end_date', '<=', now()->toDateString())
            ->get();

        $renewedCount = 0;
        $expiredCount = 0;

        foreach ($dueAgreements as $agreement) {
            // Client gave notice before the required notice deadline -> the
            // agreement genuinely ends. Otherwise (no notice, or notice sent
            // too late) it renews automatically for another term.
            if ($agreement->notice_status === 'sent' && $agreement->isNoticedAtTime()) {
                $agreement->update([
                    'agreement_status' => 'expired',
                    'finish_date' => $agreement->end_date,
                ]);
                $expiredCount++;
                continue;
            }

            $agreement->renew();
            $renewedCount++;

            $adminUser = User::where('role', 'admin')->first();
            $adminUser?->notify(new AgreementRenewed($agreement));

            if ($salesRepUser = $agreement->salesRep->user ?? null) {
                $salesRepUser->notify(new AgreementRenewed($agreement));
            }
        }

        $this->info("Processed {$dueAgreements->count()} due agreements: {$renewedCount} renewed, {$expiredCount} expired.");
    }
}
