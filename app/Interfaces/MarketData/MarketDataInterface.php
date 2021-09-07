<?php 

namespace App\Interfaces\MarketData;

use Illuminate\Support\Collection;

interface MarketDataInterface
{
    public function quote(String $symbol): Collection;

    public function formatQuote(mixed $quote): Collection;
}