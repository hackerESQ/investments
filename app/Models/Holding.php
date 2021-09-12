<?php

namespace App\Models;

use App\Models\Dividend;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Holding extends Model
{
    use HasFactory;

    protected $with = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'portfolio_id',
        'symbol',
        'quantity',
        'average_cost_basis',
        'total_cost_basis',
        'realized_gain_loss_dollars',
        'dividends_earned',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'market_data_age' => 'datetime',
    ];

    /**
     * get market data for holding
     *
     * @return void
     */
    public function market_data() 
    {
        return $this->hasOne(MarketData::class, 'symbol', 'symbol');
    }

    /**
     * get related transactions for holding
     *
     * @return void
     */
    public function transactions() 
    {
        return $this->hasMany(Transaction::class, 'symbol', 'symbol');
    }

    /**
     * get related realized gains for holding
     *
     * @return void
     */
    public function dividends() 
    {
        return $this->hasMany(Dividend::class, 'symbol', 'symbol');
    }

    public function scopePortfolio($query, $portfolio)
    {
        return $query->where('portfolio_id', $portfolio);
    }

    public function scopeSymbol($query, $symbol)
    {
        return $query->where('symbol', $symbol);
    }

    public function refreshDividends() 
    {
        return Dividend::getDividendData($this->attributes['symbol']);
    }
}
