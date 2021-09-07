<?php

namespace App\Providers;

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
        $default = config('market_data.default');
        $market_data = config("market_data.{$default}.class");

        $this->app->bind(
            \App\Interfaces\MarketData\MarketDataInterface::class,
            $market_data
        );
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
