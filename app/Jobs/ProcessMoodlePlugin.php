<?php

namespace App\Jobs;

use App\Plugin\Moodle;
use App\Searchcase;
use App\Status;
use App\Services\CaseStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessMoodlePlugin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 3;


    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //---------TODO------------------------------------------
        //Find requestdata for request
        $update = Searchcase::find(Cache::get('requestid'));
        $pluginstatus = Status::where([
            ['searchcase_id', '=', Cache::get('requestid')],
            ['plugin_id', '=', 1],
        ])->first();
        //-------------------------------------------------------
        $search = $update->request_uid;
        //Strip domainname from userid -> userid@su.se
        /* Disabled
        $searchNum = explode('@', $search);
        $search = $searchNum[0];
        */

        //Start request to Moodle
        $update->download_moodle_test = 25;
        //Update and save initiate status
        $update->save();
        $moodle = new Moodle();

        $status = $moodle->getMoodle($search);
        if ($status == 204)
        {
            //User not found
            $update->status_moodle_test = 204; //Status to searchcases
            $update->download_moodle_test = 100; //Status searchcases

            $pluginstatus->status = 204; //Status to status
            $pluginstatus->download_status = 100; //Status to status

        }
        else if( $status == 404)
        {
            //Request denied
            //$update->status_moodle_test = 404; //Should be 404 but moodle reports wrong
            //$update->status_flag = 0; //Set flag to Error
            $update->status_moodle_test = 204;
            $update->download_moodle_test = 100;

            $pluginstatus->status = 204; //Status to status
            $pluginstatus->download_status = 100; //Status to status

        }
        else
        {
            //Create folders for retrived data
            $dir = new CaseStore();
            $dir->makesystemfolder(config('services.moodle-test.client_name'));

            //Store zipfile in directory
            $dir->storeZip(config('services.moodle-test.client_name'), $status);

            //Unzip
            $dir->unzip(config('services.moodle-test.client_name'));

            //Status flags
            $update->status_moodle_test = 200; //Successful download
            $update->download_moodle_test = 100;

            $pluginstatus->status = 200; //Status to status
            $pluginstatus->download_status = 100; //Status to status

            $update->download =  $update->download+1; //Temporary finished download
        }

        //Update and save status
        $update->save();
        $pluginstatus->save();
    }
}
