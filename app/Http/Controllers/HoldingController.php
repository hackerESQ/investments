<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use App\Models\Portfolio;
use Illuminate\Http\Request;

class HoldingController extends Controller
{
    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\Portfolio  $portfolio
     * @param  \App\Models\Holding  $holding
     * @return \Illuminate\Http\Response
     */
    public function show(Portfolio $portfolio, Holding $holding)
    {
        return $holding->load([
            'transactions', 
            'dividends' => function ($q) use ($portfolio) {
                $q->where('dividends.portfolio_id', $portfolio->id);
            }
            // todo: in other portfolios
        ]);
    }
}
