<?php

namespace App\Models;

use App\Models\Dividend;
use Illuminate\Support\Facades\DB;
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
     * get related dividends for holding
     *
     * @return void
     */
    public function dividends() 
    {
        return $this->hasMany(Dividend::class, 'symbol', 'symbol');
    }

    /**
     * get related splits for holding
     *
     * @return void
     */
    public function splits() 
    {
        return $this->hasMany(Split::class, 'symbol', 'symbol');
    }

    public function scopePortfolio($query, $portfolio)
    {
        return $query->where('portfolio_id', $portfolio);
    }

    public function scopeGetPortfolioMetrics($query) 
    {
        $query->selectRaw('SUM(holdings.dividends_earned) AS total_dividends_earned')
            ->selectRaw('SUM(holdings.realized_gain_loss_dollars) AS realized_gain_loss_dollars')
            ->selectRaw('@total_market_value:=SUM(holdings.quantity * market_data.market_value) AS total_market_value')
            ->selectRaw('@sum_total_cost_basis:=SUM(holdings.total_cost_basis) AS total_cost_basis')
            ->selectRaw('@total_gain_loss_dollars:=(@total_market_value - @sum_total_cost_basis) AS total_gain_loss_dollars')
            ->selectRaw('(@total_gain_loss_dollars / @sum_total_cost_basis) * 100 AS total_gain_loss_percent')
            ->join('market_data', 'market_data.symbol', 'holdings.symbol');
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
