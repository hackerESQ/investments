<?php

return [
    'refresh' => 30, // 30 minutes

    'default' => env('MARKET_DATA', 'yahoo'),

    'yahoo' => [
        'class' => App\Interfaces\MarketData\YahooMarketData::class,
    ],
];