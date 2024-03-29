<?php

namespace App\Console;


use App\Console\Commands\RefreshMarketData;
use App\Console\Commands\CaptureDailyChange;
use App\Console\Commands\RefreshDividendData;
use App\Console\Commands\RefreshSplitData;
use Illuminate\Console\Scheduling\Schedule;
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
        $schedule->command(RefreshMarketData::class)->weekdays()->everyMinute(); // configurable in 'config.market_data'
        $schedule->command(CaptureDailyChange::class)->weekdays()->dailyAt('23:59');
        $schedule->command(RefreshDividendData::class)->weekly();
        $schedule->command(RefreshSplitData::class)->monthly();
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
