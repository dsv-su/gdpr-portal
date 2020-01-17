<?php

namespace App\Providers;

use App\Jobs\ProcessFinished;
use App\Jobs\ProcessNotFinished;
use App\Jobs\ProcessNotFound;
use App\Searchcase;
use App\Plugin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
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
            $update = Searchcase::find(Cache::get('requestid'));
            $update->setStatusProcessed();

            //Check processed flag
            $systems = Plugin::all()->count();

            if ($update->download >0 && $update->status_processed == $systems && $update->status_flag == 3)
            {
                //Successfully finished
                $update->progress = 0; //Kill progress flag
                $update->save();
                $request_finished = new ProcessFinished();
                dispatch($request_finished);

            }
            elseif ($update->download == 0 && $update->status_processed == $systems && $update->status_flag == 3)
            {
                //Sucessfully finished but user not found
                $update->progress = 0; //Kill progress flag
                $update->save();
                $request_finished_empty = new ProcessNotFound();
                dispatch($request_finished_empty);

            }
            elseif ($update->download <$systems && $update->status_processed == $systems && $update->status_flag == 0)
            {
                //Unsuccessfull -> notify
                $update->progress = 0; //Kill progress flag
                $update->save();
                $request_finished_error = new ProcessNotFinished();
                dispatch($request_finished_error);

            }
        });
    }
}
