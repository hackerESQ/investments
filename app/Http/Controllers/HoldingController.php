<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoldingController extends Controller
{
    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\Portfolio  $portfolio
     * @param  \App\Models\Holding  $holding
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Portfolio $portfolio, Holding $holding)
    {
        $holding = $holding->load([
            'market_data',
            'transactions' => function ($query) {
                $query->orderBy('date');
            }, 
            'dividends' => function ($query) {
                $query->select([
                    'dividends.symbol',
                    'dividends.date',
                    'dividends.dividend_amount',
                    ])->selectRaw('SUM(CASE WHEN transaction_type = "BUY" AND transactions.symbol = dividends.symbol AND dividends.date >= transactions.date THEN quantity ELSE 0 END) AS purchased')
                    ->selectRaw('SUM(CASE WHEN transaction_type = "SELL" AND transactions.symbol = dividends.symbol AND dividends.date >= transactions.date THEN quantity ELSE 0 END) AS sold')
                    ->join('transactions', 'transactions.symbol', 'dividends.symbol')
                    ->groupBy([
                        'dividends.symbol',
                        'dividends.date',
                        'dividends.dividend_amount',
                    ])->orderBy('date');
                // todo: only show relevant
            },
            'splits' => function ($query) {
                $query->orderBy('date');
            },
            // todo: in other portfolios
        ]);

        // dd($holding->dividends);

        return view('pages.holdings.show', [
            'portfolio' => $portfolio,
            'holding' => $holding,
        ]);
    }
}
