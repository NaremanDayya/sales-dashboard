<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Notifications\LateCustomerNotification;

class NotifyLateCustomers extends Command
{
    protected $signature = 'notify:late-customers';

    protected $description = 'Notify sales reps about clients not contacted for 3 days or more';

    public function handle()
    {
        $this->info('Starting to check for late clients...');

        $thresholdDate = now()->subDays(3)->startOfDay();

        // Get clients where last_contact_date is older than or equal to threshold date
        $lateClients = Client::whereDate('last_contact_date', '<=', $thresholdDate)->get();

        $this->info('Found ' . $lateClients->count() . ' late clients.');

        foreach ($lateClients as $client) {
            $salesRep = $client->salesRep; // Assuming relation named salesRep
            if ($salesRep) {
                $salesRep->user->notify(new LateCustomerNotification($client));
                $this->info("Notified sales rep ID {$salesRep->id} about client ID {$client->id}");
            }
        }

        $this->info('Notifications sent successfully.');

        return 0;
    }
}
