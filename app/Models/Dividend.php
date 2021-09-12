<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\MarketData\MarketDataInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'first_date' => 'datetime',
        'last_date' => 'datetime',
    ];

    /**
     * Syncs all holdings of symbol with dividend data
     *
     * @param array|self $model
     * @return void
     */
    public static function syncHoldings(mixed $model) 
    {
        // check if we got an array, if yes then lets create a dummy model
        if (is_array($model)) {
            $model = (new self)->fill($model);
        }

        // pull dividend data joined with holdings/transactions
        $dividends = self::where([
            'dividends.symbol' => $model->symbol,
        ])->select(['holdings.portfolio_id', 'dividends.date', 'dividends.symbol', 'dividends.dividend_amount'])
        ->selectRaw('@purchased:=(SELECT coalesce(SUM(quantity),0) FROM transactions WHERE transactions.transaction_type = "BUY" AND transactions.symbol = dividends.symbol AND date(transactions.date) <= date(dividends.date) AND holdings.portfolio_id = transactions.portfolio_id ) AS `purchased`')
        ->selectRaw('@sold:=(SELECT coalesce(SUM(quantity),0) FROM transactions WHERE transactions.transaction_type = "SELL" AND transactions.symbol = dividends.symbol AND date(transactions.date) <= date(dividends.date)  AND holdings.portfolio_id = transactions.portfolio_id ) AS `sold`')
        ->selectRaw('@owned:=(@purchased - @sold) AS `owned`')
        ->selectRaw('@dividends_received:=(@owned * dividends.dividend_amount) AS `dividends_received`')
        ->join('transactions', 'transactions.symbol', 'dividends.symbol')
        ->join('holdings', 'transactions.portfolio_id', 'holdings.portfolio_id')
        ->groupBy(['holdings.portfolio_id', 'dividends.date', 'dividends.symbol', 'dividends.dividend_amount'])
        ->get();

        // iterate through holdings and update 
        Holding::where(['symbol' => $model->symbol])
            ->get()
            ->each(function ($holding) use ($dividends) {
                $holding->update([
                    'dividends_earned' => $dividends->where('portfolio_id', $holding->portfolio_id)->sum('dividends_received')
                ]);
            });
    }

    /**
     * Grab new dividend data
     *
     * @param string $symbol
     * @param \DateTimeInterface|null $start_date
     * @return void
     */
    public static function getDividendData(string $symbol) 
    {
        // some dividend meta data
        $dividends = self::where(['symbol' => $symbol])
            ->selectRaw('COUNT(symbol) as total_dividends')
            ->selectRaw('MIN(date) as first_date')
            ->selectRaw('MAX(date) as last_date')
            ->get()
            ->first();

        $transactions = Transaction::where(['symbol' => $symbol])
                ->selectRaw('MIN(date) as first_date')
                ->selectRaw('MAX(date) as last_date')
                ->get()
                ->first();

        $dividend_data = collect();

        // need to fill in earlier dividends because of a new transaction
        if ($transactions->first_date->lessThan($dividends->first_date)) {

            $start_date = $transactions->first_date;
            $end_date = $dividends->first_date->subHours(48);
        } 

        // need to populate newer dividend data because of a new dividend
        if ($dividends->last_date?->lessThan($transactions->last_date)) {

            $start_date = $dividends->last_date->addHours(48);
            $end_date =  now();
        }

        // need to populate all dividend data because it didnt exist before
        if ($dividends->total_dividends == 0) {

            $start_date = $transactions->first_date;
            $end_date = now();
        }

        // get some data
        if ($start_date && $end_date) {
            $dividend_data = app(MarketDataInterface::class)->dividends($symbol, $start_date, $end_date);
        }

        // ah, we found some dividends...
        if ($dividend_data->isNotEmpty()) {
            // insert records
            (new self)->insert($dividend_data->toArray());

            // sync to holdings
            self::syncHoldings($dividend_data->last());
        }

        return $dividend_data;
    }

    public function holdings() {
        return $this->hasMany(Holding::class, 'symbol', 'symbol');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class, 'symbol', 'symbol');
    }
}
