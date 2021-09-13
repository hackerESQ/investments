<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use App\Imports\PortfolioImport;
use App\Http\Requests\PortfolioRequest;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get stats
        $metrics = Holding::query()
            ->selectRaw('SUM(holdings.dividends_earned) AS total_dividends_earned')
            ->selectRaw('SUM(holdings.realized_gain_loss_dollars) AS realized_gain_loss_dollars')
            ->selectRaw('@total_market_value:=SUM(holdings.quantity * market_data.market_value) AS total_market_value')
            ->selectRaw('@sum_total_cost_basis:=SUM(holdings.total_cost_basis) AS total_cost_basis')
            ->selectRaw('@total_gain_loss_dollars:=(@total_market_value - @sum_total_cost_basis) AS total_gain_loss_dollars')
            ->selectRaw('(@total_gain_loss_dollars / @sum_total_cost_basis) * 100 AS total_gain_loss_percent')
            ->join('market_data', 'market_data.symbol', 'holdings.symbol')
            ->first();

        return view('pages.portfolios.index', ['metrics' => $metrics]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.portfolios.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PortfolioRequest $request)
    {
        $portfolio = Portfolio::create($request->validated());

        return redirect(route('portfolio.show', $portfolio->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  Portfolio  $portfolio
     * @return \Illuminate\Http\Response
     */
    public function show(Portfolio $portfolio)
    {
        $this->authorize('view', $portfolio);

        // get stats
        $metrics= Portfolio::where(['id' => $portfolio->id])
            ->withSum('holdings as total_dividends_earned', 'dividends_earned')
            ->withSum('holdings as total_gain_loss_dollars', 'realized_gain_loss_dollars')
            ->selectRaw('@total_market_value:=(SELECT SUM(holdings.quantity * market_data.market_value) FROM holdings JOIN market_data ON market_data.symbol = holdings.symbol WHERE portfolios.id = holdings.portfolio_id) AS total_market_value')
            ->selectRaw('@sum_total_cost_basis:=(SELECT SUM(holdings.total_cost_basis) FROM holdings WHERE portfolios.id = holdings.portfolio_id) AS total_cost_basis')
            ->selectRaw('@total_gain_loss_dollars:=(@total_market_value - @sum_total_cost_basis) AS total_gain_loss_dollars')
            ->selectRaw('(@total_gain_loss_dollars / @sum_total_cost_basis) * 100 AS total_gain_loss_percent')
            ->first();

        // return view
        return view('pages.portfolios.show', [
            'portfolio' => $portfolio,
            'metrics' => $metrics
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Portfolio  $portfolio
     * @return \Illuminate\Http\Response
     */
    public function edit(Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio);

        return view('pages.portfolios.edit', ['portfolio' => $portfolio]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Portfolio  $portfolio
     * @return \Illuminate\Http\Response
     */
    public function update(PortfolioRequest $request, Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio);

        $portfolio->update($request->validated());

        return redirect(route('portfolio.show', $portfolio->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Portfolio  $portfolio
     * @return \Illuminate\Http\Response
     */
    public function destroy($portfolio)
    {
        $this->authorize('delete', $portfolio);

        $portfolio->delete();

        return redirect(route('portfolio.index'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $file = $request->file('import')->store('/', 'local');
        
        $import = (new PortfolioImport)->import($file, 'local', \Maatwebsite\Excel\Excel::XLSX);

        return redirect(route('portfolio.index')); 
    }
}
