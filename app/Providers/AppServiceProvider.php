<?php

namespace App\Providers;

use App\Jobs\ProcessFinished;
use App\Jobs\ProcessNotFinished;
use App\Jobs\ProcessNotFound;
use App\Searchcase;
use App\Plugin;
use App\Services\CaseStore;
use App\Status;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
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
            $update = Searchcase::find(Cache::get('requestid'));
            //Stats
            $update->setStatusProcessed();

            //Check processed flag
            $systems = Plugin::all()->count();

            if ($update->plugins_processed == $systems && !$update->status_flag == 0 && $update->progress > 0) {
                //| ------------------------------------------------------------------
                //| All plugins have been processed
                //| Scan statuscodes of each plugin
                //| ------------------------------------------------------------------

                $statuses = DB::table('statuses')
                            ->select('status')
                            ->where('searchcase_id', '=', Cache::get('requestid'))
                            ->get();
                $count = 0;
                foreach( $statuses as $status)
                {
                    if( $status->status == 204)
                    {
                        $count++;
                    }

                }

                if ( $count == $systems )
                    {
                        //| ---------------------------------------------------------
                        //| 1. Sucessfully finished but user not found in any system
                        //| ---------------------------------------------------------

                        // Set status flags
                        $update->setProgress(0); //Kill progress flag
                        $update->setStatusFlag(2);

                        // Remove case folders
                        $zip = new CaseStore();
                        $zip->delete_empty_case(Cache::get('requestid'));

                        //Notify user
                        $request_finished_empty = new ProcessNotFound();
                        dispatch($request_finished_empty);
                    }
                else
                    {
                        //| ---------------------------------------------------------
                        //| 2. Successfully finished
                        //| ---------------------------------------------------------

                        $update->setProgress(0); //Kill progress flag
                        $update->setStatusFlag(3);

                        $request_finished = new ProcessFinished();
                        dispatch($request_finished);
                    }

            }
            elseif ($update->plugins_processed == $systems && $update->status_flag == 0 && $update->progress > 0)
            {
                //| ----------------------------------------------------------------
                //| Unsuccessfull -> notify
                //| ----------------------------------------------------------------

                $update->setProgress(0); //Kill progress flag
                $update->setStatusFlag(0);

                // Remove case folders
                $zip = new CaseStore();
                $zip->delete_empty_case(Cache::get('requestid'));

                $request_finished_error = new ProcessNotFinished();
                dispatch($request_finished_error);

            }

        });
    }
}
