<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyChange extends Model
{
    use HasFactory;

    /**
     * Table name for the model
     *
     * @var string
     */
    protected $table = 'daily_change';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'total_market_value',
        'total_cost_basis',
        'total_gain_loss',
        'total_dividends',
        'realized_gains',
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

}
