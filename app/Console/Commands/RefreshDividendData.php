<?php

namespace App\Console\Commands;

use App\Models\Holding;
use App\Models\Dividend;
use Illuminate\Console\Command;

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
        $holdings = Holding::where('quantity', '>', 0)->get(['symbol', 'portfolio_id']);

        // $this->option('force')

        foreach ($holdings as $holding) {
            $this->line('Refreshing ' . $holding->symbol);
            Dividend::getDividendData($holding->symbol, $holding->portfolio_id);
        }
    }
}
