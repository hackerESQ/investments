<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    protected $fillable = [];

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
}
