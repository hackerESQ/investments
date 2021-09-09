<?php

namespace App\Interfaces\MarketData;

use App\Models\Holding;
use Illuminate\Support\Collection;
use Scheb\YahooFinanceApi\ApiClientFactory;

class YahooMarketData implements MarketDataInterface
{
    public $client;
    public $dividends = [];

    public function __construct() {
        // create yahoo finance client factory
        $this->client = ApiClientFactory::createApiClient();
    }

    public function exists(String $symbol): bool
    {
        return $this->quote($symbol)->isNotEmpty();
    }

    public function quote($symbol): Collection
    {
        $quote = $this->client->getQuote($symbol);

        if (empty($quote)) {
            return collect();
        }

        return collect([
            'name' => $quote->getLongName() ?? $quote->getShortName(),
            'symbol' => $quote->getSymbol(),
            'market_value' => $quote->getRegularMarketPrice(),
            'fifty_two_week_high' => $quote->getFiftyTwoWeekHigh(),
            'fifty_two_week_low' => $quote->getFiftyTwoWeekLow(),
        ]);
    }

    public function dividends($symbol, $startDate, $endDate): Collection
    {   
        $dividends = $this->client->getHistoricalDividendData($symbol, $startDate, $endDate);

        foreach ($dividends as $dividend) {
            Holding::select(['symbol', 'portfolio_id'])
                ->symbol($symbol)
                ->get()
                ->each(function($holding) use ($dividend, $symbol) {
                
                    $date = $dividend->getDate()->format('Y-m-d H:i:s');
                    $dividend_amount = $dividend->getDividends();  
                    $total_quantity_owned = $holding->calculateTotalOwnedOnDate($date);

                    array_push($this->dividends, [
                        'symbol' => $symbol,
                        'date' => $date,
                        'portfolio_id' => $holding->portfolio_id,
                        'dividend_amount' => $dividend_amount,
                        'total_quantity_owned' => $total_quantity_owned,
                        'total_received' => $total_quantity_owned * $dividend_amount,
                    ]);
                });
        }

        return collect($this->dividends);
    }
}