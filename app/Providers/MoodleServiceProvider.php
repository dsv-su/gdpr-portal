<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Plugin\Moodle;

class MoodleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Moodle::class, function ()  {
            return new Moodle();
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
