<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use App\Models\Portfolio;
use App\Models\DailyChange;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\PortfolioRequest;
use Asantibanez\LivewireCharts\Models\LineChartModel;

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
        $metrics = cache()->remember('portfolio-metrics', 60, function () {
            return Holding::getPortfolioMetrics()->withoutWishlists()->first();
        });

        $daily_changes = DailyChange::myDailyChanges()->get();

        $daily_change_chart_model = (new LineChartModel)->setTitle('Daily Gains/Losses (overall)');

        foreach($daily_changes as $daily_change) {
            $daily_change_chart_model->addPoint($daily_change->date->format('Y-m-d'), $daily_change->total_gain_loss);
        }

        return view('pages.portfolios.index', [
            'metrics' => $metrics, 
            'daily_change_chart_model' => $daily_change_chart_model
        ]);
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
        $key = 'portfolio-metrics-' . Str::kebab($portfolio->title);
        $metrics = cache()->remember($key, 60, function () use ($portfolio) {
            return Holding::where(['portfolio_id' => $portfolio->id])
                ->getPortfolioMetrics()
                ->first();
        });

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
}
