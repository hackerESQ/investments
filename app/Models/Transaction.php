<?php

namespace App\Models;

use App\Models\MarketData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
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
        $holding = Holding::firstOrNew([
            'portfolio_id' => $model->portfolio_id,
            'symbol' => $model->symbol
        ], [
            'portfolio_id' => $model->portfolio_id,
            'symbol' => $model->symbol,
            'quantity' => $model->quantity,
            'average_cost_basis' => $model->cost_basis,
            'total_cost_basis' => $model->quantity * $model->cost_basis,
        ]);

        $market_data = MarketData::getMarketData($model->symbol);

        $query = self::where([
            'portfolio_id' => $model->portfolio_id,
            'symbol' => $model->symbol,
        ])->selectRaw('SUM(CASE WHEN transaction_type = "BUY" THEN quantity ELSE 0 END) AS `qty_purchases`')
        ->selectRaw('SUM(CASE WHEN transaction_type = "SELL" THEN quantity ELSE 0 END) AS `qty_sales`')
        ->selectRaw('SUM(CASE WHEN transaction_type = "BUY" THEN (quantity * cost_basis) ELSE 0 END) AS `cost_basis`')
        ->selectRaw('SUM(CASE WHEN transaction_type = "SELL" THEN ((sale_price - cost_basis) * quantity) ELSE 0 END) AS `realized_gains`')
        ->first();

        $total_quantity = $query->qty_purchases - $query->qty_sales;
        $average_cost_basis = $query->cost_basis / $query->qty_purchases;

        $holding->fill([
            'quantity' => $total_quantity,
            'average_cost_basis' => $average_cost_basis,
            'total_cost_basis' => $total_quantity * $average_cost_basis,
            'realized_gain_loss_dollars' => $query->realized_gains,
            'dividends_earned' => Dividend::where(['symbol'=>$model->symbol,'portfolio_id'=>$model->portfolio_id])->sum('total_received'),
        ]);

        $holding->save();
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
        'transaction_type',
        'quantity',
        'cost_basis',
        'sale_price'
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
    protected $casts = [];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function setSymbolAttribute($value) 
    {
        $this->attributes['symbol'] = strtoupper($value);
    }

    /**
     * get market data for transaction
     *
     * @return void
     */
    public function market_data() {
        return $this->hasOne(MarketData::class, 'symbol', 'symbol');
    }

    public function scopePortfolio($query, $portfolio)
    {
        return $query->where('portfolio_id', $portfolio);
    }

    public function scopeSymbol($query, $symbol)
    {
        return $query->where('symbol', $symbol);
    }
}
