<?php

namespace App\Console\Commands;

use App\Models\MarketData;
use Illuminate\Console\Command;

class RefreshMarketData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market-data:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh market data from market data provider';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $symbols = MarketData::all('symbol');

        foreach ($symbols as $symbol) {
            $symbol->refreshMarketData();
        }
    }
}
