<?php

namespace App\Models;

use App\Interfaces\MarketData\MarketDataInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketData extends Model
{
    use HasFactory;

    protected $primaryKey = 'symbol';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'symbol',
        'name',
        'market_value',
        'fifty_two_week_high',
        'fifty_two_week_low',
    ];

    public function refreshMarketData() {

        return static::getMarketData($this->attributes['symbol']);

    }

    public static function getMarketData($symbol) 
    {
        $market_data = self::firstOrNew(['symbol' => $symbol]);

        // check if new or stale
        if (!$market_data->exists || now()->diffInMinutes($market_data->updated_at) >= config('market_data.refresh')) {
            
            // get quote
            $quote = app(MarketDataInterface::class)->quote($symbol);

            // fill data
            $market_data->fill($quote->toArray());

            // save with timestamps updated
            $market_data->touch();
        }

        return $market_data;
    }

    public function holdings() {
        return $this->hasMany(Holding::class, 'symbol', 'symbol')->where('holdings.quantity', '>', 0);
    }
}