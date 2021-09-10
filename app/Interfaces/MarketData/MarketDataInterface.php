<?php 

namespace App\Interfaces\MarketData;

use Illuminate\Support\Collection;

interface MarketDataInterface
{

    /**
     * Does this symbol actually exist?
     */
    public function exists(String $symbol): bool;

    /**
     * Get quote data
     */
    public function quote(String $symbol): Collection;

    /**
     * Get dividend data
     */
    public function dividends(String $symbol, \DateTimeInterface $startDate, \DateTimeInterface $endDate): Collection;

    /**
     * Get split data
     */
    public function splits(String $symbol, \DateTimeInterface $startDate, \DateTimeInterface $endDate): Collection;
}