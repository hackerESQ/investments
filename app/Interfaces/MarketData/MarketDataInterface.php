<?php 

namespace App\Interfaces\MarketData;

use Illuminate\Support\Collection;

interface MarketDataInterface
{
    /**
     * Get quote data
     */
    public function quote(String $symbol): Collection;

    /**
     * Get dividend data
     */
    public function dividendHistory(String $symbol, \DateTimeInterface $startDate, \DateTimeInterface $endDate): Collection;
}