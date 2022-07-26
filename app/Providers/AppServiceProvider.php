<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        If (env('APP_ENV') !== 'local') {
//            $this->app['request']->server->set('HTTPS', true);
//        }

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('HttpClient', function ($app, $options = null) {
            $opts['headers']['User-Agent'] = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36';
            $opts['connect_timeout'] = 30;
            $opts['read_timeout'] = 20;
            $opts['timeout'] = 40;
            $options = array_merge($opts, $options);

            return new \GuzzleHttp\Client($options);
        });

        $this->app->alias(
            'HttpClient',
            \GuzzleHttp\Client::class
        );
    }
}
