<?php

namespace App\Console;

use App\Console\Commands\RefreshMarketData;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\RefreshDividendData;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(RefreshMarketData::class)->everyThirtyMinutes();
        $schedule->command(RefreshDividendData::class)->daily();
        $schedule->command(RefreshSplitData::class)->weekly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
