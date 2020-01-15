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
        $case = Searchcase::find(Cache::get('requestid'));
        $casestatus = Status::where([
            ['searchcase_id', '=', Cache::get('requestid')],
            ['plugin_id', '=', 1],
        ])->first();
        //-------------------------------------------------------
        $search = $case->request_uid;
        //Strip domainname from userid -> userid@su.se
        /* Disabled
        $searchNum = explode('@', $search);
        $search = $searchNum[0];
        */

        //Start request to Moodle
        $casestatus->setDownloadStatus(25);

        $moodle = new Moodle();

        $status = $moodle->getMoodle($search);
        if ($status == 204)
        {
            //User not found
            $casestatus->setStatus(204);
            $casestatus->setDownloadStatus(100);
            $case->setStatusFlag(3); //Successful but not found
            $case->setDownloadSuccess(); //Successful but not found
        }
        else if( $status == 404)
        {
            //Request denied
            $case->setDownloadFail(); //Download error
            $casestatus->setStatus(204);
            $casestatus->setDownloadStatus(100);

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
            $case->setStatusFlag(3);
            $case->setDownloadSuccess();
            $casestatus->setStatus(200);
            $casestatus->setDownloadStatus(100);

        }

    }
}
