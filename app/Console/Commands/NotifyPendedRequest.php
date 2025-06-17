<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ClientEditRequest;
use App\Models\AgreementEditRequest;
use App\Notifications\PendedRequestNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class NotifyPendedRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:pended-request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify admin about client or agreement requests pending for more than 3 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threeDaysAgo = Carbon::now()->subDays(3);

        // Get all requests older than 3 days and still pending
        $staleClientRequests = ClientEditRequest::where('status', 'pending')
            ->where('created_at', '<=', $threeDaysAgo)
            ->get();

        $staleAgreementRequests = AgreementEditRequest::where('status', 'pending')
            ->where('created_at', '<=', $threeDaysAgo)
            ->get();

        // If there are any stale requests, notify admins
        if ($staleClientRequests->isNotEmpty() || $staleAgreementRequests->isNotEmpty()) {
            // Get all admins (or define your own admin check)
            $admins = User::where('role', 'admin')->get(); // adjust as needed

            Notification::send($admins, new PendedRequestNotification(
                $staleClientRequests,
                $staleAgreementRequests
            ));

            $this->info('Admins notified about pending requests.');
        } else {
            $this->info('No pending requests older than 3 days.');
        }
    }
}
