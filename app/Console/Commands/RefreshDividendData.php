<?php

namespace App\Console\Commands;

use App\Models\MarketData;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;

class RefreshDividendData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market-data:refresh
                            {--force= : Ignore refresh delay}';

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
        // get all symbols in market data where holding qty > 1
        $symbols = MarketData::with(['holdings'])->whereHas('holdings', function($query) {
            $query->where('quantity', '>', 0);
        })->get(['symbol']);

        // $this->option('force')

        foreach ($symbols as $symbol) {
            $this->line('Refreshing ' . $symbol->symbol);
            $symbol->refreshMarketData();
        }
    }
}
