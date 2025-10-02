<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agreement;
use App\Models\User;
use App\Notifications\AgreementNoticePeriodStarted;
use Illuminate\Support\Facades\DB;

class NotifyAgreementNoticePeriod extends Command
{
    protected $signature = 'notify:agreement-notice';
    protected $description = 'Notify admins and sales reps when agreement notice period starts';

    public function handle()
    {
        $today = now()->startOfDay();

        $agreements = Agreement::whereRaw(
            "DATE_ADD(implementation_date, INTERVAL notice_months MONTH) = ?",
            [$today->toDateString()]
        )->get();

        foreach ($agreements as $agreement) {
            // Notify Admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AgreementNoticePeriodStarted($agreement));
            }

            // Notify Sales Rep
            if ($agreement->salesRep) {
                $agreement->salesRep->user->notify(new AgreementNoticePeriodStarted($agreement));
            }
        }

        $this->info("Notice period notifications sent.");
    }
}
