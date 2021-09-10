<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\MarketData\MarketDataInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Dividend extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'symbol',
        'date',
        'dividend_amount',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    public static function syncHoldings($model) 
    {
        // check if we got an array, then lets create a dummy model
        if (is_array($model)) {
            $model = (new self)->fill($model);
        }

        // pull dividend data joined with holdings/transactions
        $dividends = self::where([
            'dividends.symbol' => $model->symbol,
        ])->select(['holdings.portfolio_id', 'dividends.date', 'dividends.symbol', 'dividends.dividend_amount'])
        ->selectRaw('@purchased:=(SELECT coalesce(SUM(quantity),0) FROM transactions WHERE transactions.transaction_type = "BUY" AND transactions.symbol = dividends.symbol AND date(transactions.date) <= date(dividends.date) AND holdings.portfolio_id = transactions.portfolio_id ) AS `qty_purchases`')
        ->selectRaw('@sold:=(SELECT coalesce(SUM(quantity),0) FROM transactions WHERE transactions.transaction_type = "SELL" AND transactions.symbol = dividends.symbol AND date(transactions.date) <= date(dividends.date)  AND holdings.portfolio_id = transactions.portfolio_id ) AS `qty_sold`')
        ->selectRaw('@owned:=(@purchased - @sold) AS `qty_owned`')
        ->selectRaw('@dividends_received:=(@owned * dividends.dividend_amount) AS `dividends_received`')
        ->join('transactions', 'transactions.symbol', 'dividends.symbol')
        ->join('holdings', 'transactions.portfolio_id', 'holdings.portfolio_id')
        ->groupBy(['holdings.portfolio_id', 'dividends.date', 'dividends.symbol', 'dividends.dividend_amount'])
        ->get();

        // iterate through holdings and update 
        Holding::where([
            'symbol' => $model->symbol
        ])->get()
        ->each(function ($holding) use ($dividends) {
            $holding->update([
                'dividends_earned' => $dividends->where('portfolio_id', $holding->portfolio_id)->sum('dividends_received')
            ]);
        });
    }

    public static function getDividendData(string $symbol, \DateTimeInterface $start_date = null) 
    {
        // most recent dividend date for given symbol and portfolio (last dividend date)
        $last_dividend_date = self::where([
                'symbol' => $symbol, 
                // 'portfolio_id' => $portfolio_id
            ])->latest('date')->first()?->date->addHours(48);

        // start date not provided, try to get last dividend date as starting point
        if (!$start_date) {
            $start_date = $last_dividend_date;
        }

        // no dividends on record, try to use oldest transaction date
        if (!$start_date) {
            $start_date = Transaction::where('symbol', $symbol)->oldest('date')->first()?->date;
        }

        // welp... let's fail early
        if (!$start_date) {
            throw new HttpException(500, 'No valid start date provided');
        }
    
        // fetch new dividend data
        $dividend_data = app(MarketDataInterface::class)->dividends($symbol, $start_date, now());

        // no dividends, nothing to sync, let's exit...
        if ($dividend_data->isEmpty()) {
            return collect();
        }

        // add records
        (new self)->insert($dividend_data->toArray());

        // sync to holdings
        self::syncHoldings($dividend_data->last());

        return $dividend_data;
    }

    public function holdings() {
        return $this->hasMany(Holding::class, 'symbol', 'symbol');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class, 'symbol', 'symbol');
    }
}
