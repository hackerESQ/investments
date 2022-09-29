<?php

namespace App\Interfaces\MarketData;

use App\Models\Holding;
use Illuminate\Support\Collection;

class FakeMarketData implements MarketDataInterface
{

    // scratch pad for dividends
    public $dividends = [];

    // scratch pad for splits
    public $splits = [];

    public function exists(String $symbol): bool
    {
        return $symbol ? true : false;
    }

    public function quote($symbol): Collection
    {
        
        return collect([
            'name' => 'Test Security',
            'symbol' => $symbol,
            'market_value' => 200.00,
            'fifty_two_week_high' => 210.00,
            'fifty_two_week_low' => 175.00,
        ]);
    }

    public function dividends($symbol, $startDate, $endDate): Collection
    {
        return collect([[
            'symbol' => $symbol,
            'date' => '2022-08-01',
            'dividend_amount' => .50,
        ],
        [
            'symbol' => $symbol,
            'date' => '2022-06-01',
            'dividend_amount' => .45,
        ]]);
    }

    public function splits($symbol, $startDate, $endDate): Collection
    {   
        return collect([
            'symbol' => $symbol,
            'date' => '2020-01-01',
            'split_amount' => 4,
        ]);
    }
}