<?php

namespace App\Console\Commands;

use App\Models\Holding;
use App\Models\Dividend;
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
    protected $signature = 'dividend-data:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh dividend data from data provider';

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
        // get all symbols in holdings where holding qty > 1
        $symbols = Holding::where('quantity', '>', 0)->distinct('symbol')->get(['symbol']);

        // $this->option('force')

        foreach ($symbols as $symbol) {
            $this->line('Refreshing ' . $symbol->symbol);
            Dividend::getDividendData($symbol->symbol);
        }
    }
}
