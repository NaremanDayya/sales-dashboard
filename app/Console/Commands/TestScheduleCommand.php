<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         $this->info('Scheduler is working! Time: '. now());

        return 0;
    }
}
