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
     * get market data for holding
     *
     * @return void
     */
    public function market_data() {
        return $this->hasOne(MarketData::class, 'symbol', 'symbol');
    }

    /**
     * get related transactions for holding
     *
     * @return void
     */
    public function transactions() {
        return $this->hasMany(Transaction::class, 'symbol', 'symbol');
    }

    /**
     * get related realized gains for holding
     *
     * @return void
     */
    public function dividends() {
        return $this->hasMany(Dividend::class, 'symbol', 'symbol');
    }

    public function scopePortfolio($query, $arg)
    {
        return $query->where('portfolio_id', $arg);
    }

    public function scopeSymbol($query, $arg)
    {
        return $query->where('symbol', $arg);
    }

    public function calculateTotalOwnedOnDate($date) 
    {
        return Transaction::select(['symbol', 'portfolio_id', 'transaction_type', 'quantity'])
            ->portfolio($this->attributes['portfolio_id'])
            ->symbol($this->attributes['symbol'])
            ->whereDate('date', '<', $date)
            ->get()
            ->reduce(function ($carry, $item) {
                return $item->transaction_type == 'BUY' ? $carry + $item->quantity : $carry - $item->quantity;
            });
    }

    public function refreshDividends() {
        return Dividend::getDividendData($this->attributes['symbol'], $this->attributes['portfolio_id']);
    }
}
