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
        'portfolio_id',
        'dividend_amount',
        'total_quantity_owned',
        'total_received',
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

    /**
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            static::syncHolding($model);
        });

        static::deleted(function ($model) {
            static::syncHolding($model);
        });
    }

    public static function syncHolding($model) 
    {
        // check if we got an array, then lets create a dummy model
        if (is_array($model)) {
            $model = (new self)->fill($model);
        }

        // calculate total dividends received
        $dividends = self::where([
            'portfolio_id' => $model->portfolio_id,
            'symbol' => $model->symbol,
        ])->selectRaw('SUM(total_received) AS `dividends_earned`')
        ->first();

        // update holding
        Holding::where([
            'portfolio_id' => $model->portfolio_id,
            'symbol' => $model->symbol
        ])
        ->firstOrFail()
        ->update([
            'dividends_earned' => $dividends->dividends_earned,
        ]);
    }

    public static function getDividendData(string $symbol, int $portfolio_id, \DateTimeInterface $start_date = null) 
    {
        // most recent dividend date for given symbol and portfolio (last dividend date)
        $last_dividend_date = self::where([
                'symbol' => $symbol, 
                'portfolio_id' => $portfolio_id
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

        // if start date is less than last dividend date, then 
        // we only need to UPDATE existing records (dont want to pull in duplicates)
        if (Carbon::parse($start_date)->lessThan($last_dividend_date)) {
            // update each existing dividend
            $dividend_data = self::where([
                'symbol' => $symbol,
                'portfolio_id' => $portfolio_id,
            ])
            ->whereBetween('date', [$start_date, $last_dividend_date])
            ->get()
            ->each(function($dividend) {
                
                $total_quantity_owned = $dividend->getHolding()->calculateTotalOwnedOnDate($dividend->date);

                $dividend->fill([
                    'total_quantity_owned' => $total_quantity_owned,
                    'total_received' => $total_quantity_owned * $dividend->dividend_amount
                ]);

                $dividend->saveQuietly(); // will be syncing holdings later
            });
    
            // do we need to add any missing dividends that pre-date our records?
            $first_dividend_date = self::where([
                'symbol' => $symbol, 
                'portfolio_id' => $portfolio_id
            ])->oldest('date')->first()?->date;

            if (Carbon::parse($start_date)->lessThan($first_dividend_date)) {
                // load missing as a supplement
                $supplement = app(MarketDataInterface::class)->dividends($symbol, $start_date, $first_dividend_date);

                (new self)->insert($supplement->toArray());
            }
        } else {
            // fetch new dividend data
            $dividend_data = app(MarketDataInterface::class)->dividends($symbol, $start_date, now());

            // add records
            (new self)->insert($dividend_data->toArray());
        }

        // no dividends, exit
        if ($dividend_data->isEmpty()) {
            return collect();
        }

        self::syncHolding($dividend_data->last());

        return $dividend_data;
    }

    public function holdings() {
        return $this->hasMany(Holding::class, 'symbol', 'symbol');
    }

    public function getHolding() {
        return Holding::symbol($this->attributes['symbol'])->portfolio($this->attributes['portfolio_id'])->first();
    }
}
