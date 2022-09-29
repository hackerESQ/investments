<?php

return [
    'refresh' => 30, // 30 minutes

    'default' => env('MARKET_DATA', 'yahoo'),

    'yahoo' => App\Interfaces\MarketData\YahooMarketData::class,
    'fake' => App\Interfaces\MarketData\FakeMarketData::class,
];