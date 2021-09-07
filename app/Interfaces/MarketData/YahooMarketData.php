<?php

namespace App\Interfaces\MarketData;

use Illuminate\Support\Collection;
use Scheb\YahooFinanceApi\ApiClientFactory;

class YahooMarketData implements MarketDataInterface
{
    public function quote($symbol): Collection
    {

        // Create a new client from the factory
        $client = ApiClientFactory::createApiClient();

        // // Returns an array of Scheb\YahooFinanceApi\Results\SearchResult
        // $searchResult = $client->search("Apple");

        // Returns an array of Scheb\YahooFinanceApi\Results\HistoricalData
        return $this->formatQuote($client->getQuote($symbol));
    }

    public function formatQuote($quote): Collection
    {
        if (!$quote) {
            return collect();
        }

        return collect([
            'name' => $quote->getShortName(),
            'market_value' => $quote->getRegularMarketPrice(),
            '52_week_high' => $quote->getFiftyTwoWeekHigh(),
            '52_week_low' => $quote->getFiftyTwoWeekLow(),
        ]);
    }
}