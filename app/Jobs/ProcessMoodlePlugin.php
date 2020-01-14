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
        $pluginstatus->download_status = 25;

        //Update and save initiate status
        $update->save();
        $pluginstatus->save();

        $moodle = new Moodle();

        $status = $moodle->getMoodle($search);
        if ($status == 204)
        {
            //User not found
            $pluginstatus->status = 204; //Status to status
            $update->status_flag = 3; //Successful but not found
            $pluginstatus->download_status = 100; //Status to status
            $update->download =  $update->download+1; //Temporary finished download

        }
        else if( $status == 404)
        {
            //Request denied

            //$update->status_flag = 0; //Set flag to Error
            $update->download =  0; //Download error
            $pluginstatus->status = 204; //Status to status
            $pluginstatus->download_status = 100; //Status to status

        }
        else
        {
            //********************************************************************
            //Sucessfull download
            //********************************************************************

            //Create folders for retrived data
            $dir = new CaseStore();
            $dir->makesystemfolder(config('services.moodle-test.client_name'));

            //Store zipfile in directory
            $dir->storeZip(config('services.moodle-test.client_name'), $status);

            //Unzip
            $dir->unzip(config('services.moodle-test.client_name'));

            //Status flags
            //$update->download_moodle_test = 100;
            $update->status_flag = 3; //Successful

            $pluginstatus->status = 200; //Status to status
            $pluginstatus->download_status = 100; //Status to status

            $update->download =  $update->download+1; //Temporary finished download
        }

        //Update and save status
        $update->save();
        $pluginstatus->save();
    }
}
