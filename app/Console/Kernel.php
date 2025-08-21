<?php

namespace App\Console;
\file_put_contents(storage_path('logs/kernel_loaded.txt'), 'Kernel loaded at '.now().PHP_EOL, FILE_APPEND);

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;


class Kernel extends ConsoleKernel
{
protected $commands = [
    \App\Console\Commands\TestScheduleCommand::class,
];

    protected function schedule(Schedule $schedule)
    {
  logger()->info('Scheduler initialized at '.now());
    
    $schedule->command('app:test-schedule-command')
        ->everyMinute()
        ->before(function () {
            logger()->info('Command about to run');
        })
        ->after(function () {
            logger()->info('Command finished');
        });


    }
  
 protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }

   
}
