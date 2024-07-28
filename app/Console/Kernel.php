<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // test command
        $schedule->command('test:for-testing-purpose')->twiceDaily(8, 20);

        // monthly user reminder for 3 days before expire
        $schedule->command('app:monthly-reminder expiring')->dailyAt('07:00');

        // monthly user reminder for expired pass
        $schedule->command('app:monthly-reminder expired')->dailyAt('00:00');

        // create sales record monthly/daily
        $schedule->command('app:create-sales-record')->dailyAt('00:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
