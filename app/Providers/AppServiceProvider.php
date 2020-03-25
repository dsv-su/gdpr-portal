<?php

namespace App\Providers;

use App\Jobs\ProcessFinished;
use App\Jobs\ProcessNotFinished;
use App\Jobs\ProcessNotFound;
use App\Searchcase;
use App\Plugin;
use App\Services\CaseStore;
use App\Services\CheckProcessedStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::before(function (JobProcessing $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
        });

        Queue::after(function (JobProcessed $event) {
            // $event->connectionName  //database
            // $event->job
            //$event->job->payload()

            //Find requestdata for request and update stats
            $update = Searchcase::find(Cache::get('caseid'));
            $check = new CheckProcessedStatus($update);
            $check->status();

        });

        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception
        });
    }
}
