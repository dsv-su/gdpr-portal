<?php

namespace App\Services;

use App\Jobs\ProcessFinished;
use App\Jobs\ProcessNotFinished;
use App\Jobs\ProcessNotFound;
use App\Plugin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

Class CheckProcessedStatus extends Model
{
    protected $case;

    public function __construct($case)
    {
        $this->case = $case;
    }

    public function status()
    {
        $update = $this->case;
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
                ->where('searchcase_id', '=', $update->id)
                ->get();

            $count = 0;
            foreach ($statuses as $status) {
                if ($status->status == 204 or $status->status == 307) {
                    $count++;
                }

            }

            if ($count == $systems) {
                //| ---------------------------------------------------------
                //| 1. Sucessfully finished but user not found in any system
                //| ---------------------------------------------------------

                // Set status flags
                $update->setProgress(0); //Kill progress flag
                $update->setStatusFlag(2);

                // Remove case folders
                $zip = new CaseStore($update);
                $zip->delete_empty_case($update->id);

                //Notify user
                $request_finished_empty = new ProcessNotFound($update);
                dispatch($request_finished_empty);
            } else {
                //| ---------------------------------------------------------
                //| 2. Successfully finished
                //| ---------------------------------------------------------

                $update->setProgress(0); //Kill progress flag
                $update->setStatusFlag(3);

                $request_finished = new ProcessFinished($update);
                dispatch($request_finished);
            }

        }
        elseif ($update->status_flag == 0 && $update->progress > 0) {
            //| ----------------------------------------------------------------
            //| Unsuccessfull -> notify
            //| ----------------------------------------------------------------

            $update->setProgress(0); //Kill progress flag
            $update->setStatusFlag(0);

            // Remove case folders -> Uncomment to remove Override function
            //$zip = new CaseStore($update);
            //$zip->delete_empty_case($update->id);

            $request_finished_error = new ProcessNotFinished($update);
            dispatch($request_finished_error);

        }
    }

}
