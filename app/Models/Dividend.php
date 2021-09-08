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
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            static::calcHoldings($model);
        });

        static::deleted(function ($model) {
            static::calcHoldings($model);
        });
    }

    public static function calcHoldings($model) {
        // get the holding for a symbol and portfolio
        $holding = Holding::where([
            'portfolio_id' => $model->portfolio_id,
            'symbol' => $model->symbol
        ])->firstOrFail();

        $query = self::where([
            'portfolio_id' => $model->portfolio_id,
            'symbol' => $model->symbol,
        ])->selectRaw('SUM(total_received) AS `dividends_earned`')
        ->first();

        // update holding
        $holding->update([
            'dividends_earned' => $query->dividends_earned,
        ]);
    }

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

    public function refreshDividendData() {

        return static::getDividendData($this->attributes['symbol']);

    }

    public static function getDividendData($symbol) 
    {
        // try to get last dividend date as starting point
        $start_date = self::where('symbol', $symbol)->latest('date')->first()?->date->addHours(48);

        // or use oldest transaction date
        if (!$start_date) {
            $start_date = Transaction::where('symbol', $symbol)->oldest('date')->first()?->date;
        }

        // welp... let's fail early
        if (!$start_date) {
            throw new HttpException(500, 'No valid start date provided');
        }

        // get dividend
        $dividend_data = app(MarketDataInterface::class)->dividends($symbol, $start_date, now());

        // save data
        foreach($dividend_data->toArray() as $dividend) {
            (new self)->fill($dividend)->save();
        }
        
        return $dividend_data;
    }

    public function holdings() {
        return $this->hasMany(Holding::class, 'symbol', 'symbol');
    }
}
