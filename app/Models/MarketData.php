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
        '52_week_high',
        '52_week_low',
    ];

    public function refreshMarketData() {

        return static::loadMarketData($this->attributes['symbol']);
        
    }

    public static function loadMarketData($symbol) 
    {
        $market_data = self::firstOrNew(['symbol' => $symbol]);

        // check if new or stale
        if (!$market_data->exists || now()->diffInMinutes($market_data->updated_at) >= config('market_data.refresh')) {
            // get quote
            $quote = app(MarketDataInterface::class)->quote($symbol);

            // fill data
            $market_data->fill([
                'symbol' => $symbol,
                'name' => $quote->get('name'),
                'market_value' => $quote->get('market_value'),
                '52_week_high' => $quote->get('52_week_high'),
                '52_week_low' => $quote->get('52_week_low'),
            ]);

            // save
            $market_data->save();
        }

        return $market_data;
    }
}