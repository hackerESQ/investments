<?php

return [
    'refresh' => 60, // 60 minutes

    'default' => env('MARKET_DATA', 'yahoo'),

    'yahoo' => [
        'class' => App\Interfaces\MarketData\YahooMarketData::class,
    ],
];