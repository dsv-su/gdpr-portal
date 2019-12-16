<?php

namespace App\Jobs;

use App\Plugin\Moodle;
use App\Searchcase;
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
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Find requestdata for request
        $update = Searchcase::find(Cache::get('requestid'));
        //Strip domainname from userid -> userid@su.se
        $search = $update->request_uid;
        $searchNum = explode('@', $search);
        $search = $searchNum[0];

        //Start request to Moodle
        $update->download_moodle_test = 25;
        //Update and save initiate status
        $update->save();
        $moodle = new Moodle();

        $status = $moodle->getMoodle($search);
        if ($status == 204)
        {
            //User not found
            $update->status_moodle_test = 204;
            $update->download_moodle_test = 100;
        }
        else if( $status == 404)
        {
            //Request denied
            $update->status_moodle_test = 404;
            $update->download_moodle_test = 100;
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
            $update->download =  $update->download+1; //Temporary finished download
        }

        //Update and save status
        $update->save();
    }
}
