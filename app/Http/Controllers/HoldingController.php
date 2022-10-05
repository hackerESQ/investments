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
        $holding = $portfolio->holdings()
                            ->where(['symbol' => $holding->symbol])
                            ->with([
                                'market_data',
                                'transactions' => function ($query) use ($portfolio) {
                                    $query->portfolio($portfolio->id)->orderBy('date', 'DESC');
                                }, 
                                'dividends' => function ($query) use ($portfolio, $holding) {
                                    $query->select([
                                        'dividends.symbol',
                                        'dividends.date',
                                        'dividends.dividend_amount',
                                        ])
                                        ->selectRaw('SUM(CASE WHEN transaction_type = "BUY" AND transactions.symbol = dividends.symbol AND transactions.portfolio_id = ' . $portfolio->id . ' AND dividends.date >= transactions.date THEN quantity ELSE 0 END) AS purchased')
                                        ->selectRaw('SUM(CASE WHEN transaction_type = "SELL" AND transactions.symbol = dividends.symbol AND transactions.portfolio_id = ' . $portfolio->id . '  AND dividends.date >= transactions.date THEN quantity ELSE 0 END) AS sold')
                                        ->join('transactions', 'transactions.symbol', 'dividends.symbol')
                                        ->groupBy([
                                            'dividends.symbol',
                                            'dividends.date',
                                            'dividends.dividend_amount',
                                        ])->orderBy('dividends.date', 'DESC')
                                        ->where('dividends.date', '>=', function ($query) use ($portfolio, $holding) {
                                            $query->selectRaw('min(transactions.date)')
                                                ->from('transactions')
                                                ->whereRaw('transactions.portfolio_id = ' . $portfolio->id)
                                                ->whereRaw('transactions.symbol = \'' . $holding->symbol . '\'');
                                        });
                                },
                                'splits' => function ($query) {
                                    $query->orderBy('date', 'DESC');
                                },
                                // todo: in other portfolios
                            ])->firstOrFail();

        return view('pages.holdings.show', [
            'portfolio' => $portfolio,
            'holding' => $holding,
        ]);
    }
}
