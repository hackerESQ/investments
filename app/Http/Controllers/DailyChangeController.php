<?php

namespace App\Http\Controllers;

use App\Models\DailyChange;
use Asantibanez\LivewireCharts\Models\LineChartModel;

class DailyChangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $daily_changes = DailyChange::myDailyChanges()->get();

        $daily_change_chart_model = (new LineChartModel)->setTitle('Daily Gains/Losses (overall)');

        foreach($daily_changes as $daily_change) {
            $daily_change_chart_model->addPoint($daily_change->date->format('Y-m-d'), $daily_change->total_gain_loss);
        }
        
        return view('pages.daily_change.index', [
            'daily_change_chart_model' => $daily_change_chart_model
        ]);
    }
}
