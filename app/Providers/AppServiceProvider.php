<?php

namespace App\Providers;

use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $market_data = config(
            "market_data." . 
            config('market_data.default', 'yahoo')
        );

        $this->app->bind(
            \App\Interfaces\MarketData\MarketDataInterface::class,
            $market_data
        );

        // hidden registration
        if (Request::input('registration_key') == config('auth.registration_key')) {
            config([
                'fortify.features' => array_merge(config('fortify.features'), [Features::registration()])
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
