<?php


namespace App\Providers;
use Atk4;


class Data extends \Illuminate\Support\ServiceProvider
{
    function register()
    {
        $this->app->configure('db');
        $this->app->singleton('db',
            function ($app) {

                $dsn = env('DB');
                return Atk4\Data\Persistence::connect($dsn);
            }
        );
        $this->app->alias('db', Atk4\Data\Persistence::class);
    }
}
