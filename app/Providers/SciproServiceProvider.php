<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Plugin\Scipro;

class SciproServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Scipro::class, function ()  {
            return new Scipro();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
