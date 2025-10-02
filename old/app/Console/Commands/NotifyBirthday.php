<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BirthdayGreeting;
use Carbon\Carbon;

class NotifyBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
	 protected $signature = 'notify:birthday';

    // The console command description.
    protected $description = 'Send birthday greetings to users who have birthday today';

    public function handle(): void
    {
        $today = Carbon::today();

        $users = User::whereMonth('birthday', $today->month)
            ->whereDay('birthday', $today->day)
            ->get();

        foreach ($users as $user) {
            Notification::send($user, new BirthdayGreeting());
        }

        $this->info('Birthday notifications sent successfully.');
    }
}
