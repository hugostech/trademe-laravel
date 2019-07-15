<?php

namespace Hugostech\Trademe;


use Illuminate\Support\ServiceProvider;

class TrademeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TradeMe::class, function ($app){
            return new TradeMe();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/trademe.php' => config_path('trademe.php'),
        ]);
    }
}
