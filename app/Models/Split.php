<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\MarketData\MarketDataInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Split extends Model
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
        'split_amount',
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
        'first_date' => 'datetime',
        'last_date' => 'datetime',
    ];

    /**
     * Syncs all transactions of symbol with split data
     *
     * @param string $symbol
     * @return void
     */
    public static function syncToTransactions($symbol) 
    {
        // pull split data joined 
        $splits = Transaction::where([
            'splits.symbol' => $symbol,
        ])
        ->whereDate('transactions.date', '<=', DB::raw('splits.date'))
        ->whereDate('transactions.date', '>', DB::raw('IFNULL(market_data.splits_synced_to_holdings_at, "0000-00-00")'))
        ->select(['transactions.id as transaction_id', 'splits.date as split_date', 'splits.symbol', 'transactions.cost_basis', 'transactions.quantity', 'splits.split_amount', 'transactions.date as transaction_date'])
        ->join('splits', 'transactions.symbol', 'splits.symbol')
        ->join('market_data', 'transactions.symbol', 'market_data.symbol')
        ->groupBy(['transactions.id', 'splits.date', 'splits.symbol', 'transactions.cost_basis', 'transactions.quantity', 'splits.split_amount', 'transactions.date'])
        ->get();

        // iterate through transactions and update each with splits
        Transaction::where(['symbol' => $symbol])
            ->get()
            ->each(function ($transaction) use ($splits) {
                $splits->where('transaction_id', $transaction->id)->sortBy('split_date')->each(function($split) use ($transaction) {
                    dump($split->split_amount);
                    $transaction->update([    
                        'quantity' => $transaction->quantity * $split->split_amount,
                        'cost_basis' => $transaction->cost_basis / $split->split_amount
                    ]);
                });
            });

        // update market data with latest date
        MarketData::setSplitsHoldingSynced($symbol);
    }

    /**
     * Grab new split data
     *
     * @param string $symbol
     * @param \DateTimeInterface|null $start_date
     * @return void
     */
    public static function getSplitData(string $symbol) 
    {
        // dates for split data
        $splits_meta = self::where(['symbol' => $symbol])
            ->selectRaw('COUNT(symbol) as total_splits')
            ->selectRaw('MIN(date) as first_date')
            ->selectRaw('MAX(date) as last_date')
            ->get()
            ->first();
     
        $transactions_meta = Transaction::where(['symbol' => $symbol])
            ->selectRaw('MIN(date) as first_date')
            ->selectRaw('MAX(date) as last_date')
            ->get()
            ->first();

        $split_data = collect();

        if ($splits_meta->total_splits != 0) {
            // need to fill in earlier splits 
            if ($transactions_meta->first_date->lessThan($splits_meta->first_date)) {

                $start_date = $transactions_meta->first_date;
                $end_date = $splits_meta->first_date->subHours(48);
            } 

            // need to populate newer split data
            if ($splits_meta->last_date?->lessThan($transactions_meta->last_date)) {

                $start_date = $splits_meta->last_date->addHours(48);
                $end_date =  now();
            }

        // need to populate all split data because it didnt exist before
        } else {

            $start_date = $transactions_meta->first_date;
            $end_date = now();
        }

        // get some data
        if ($start_date && $end_date) {
            $split_data = app(MarketDataInterface::class)->splits($symbol, $start_date, $end_date);
        }

        if ($split_data->isNotEmpty()) {
            // insert records
            (new self)->insert($split_data->toArray());   
            
        }

        // sync to transactions
        self::syncToTransactions($symbol);

        return $split_data;
    }

    public function holdings() {
        return $this->hasMany(Holding::class, 'symbol', 'symbol');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class, 'symbol', 'symbol');
    }
}
