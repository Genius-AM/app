<?php

namespace App\Providers;

use App\Core\Notifications\SmscApi;
use Illuminate\Support\ServiceProvider;

class SmscRuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SmscApi::class, function ($app) {
            return new SmscApi();
        });
    }
}
