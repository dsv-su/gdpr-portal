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

        $update = Searchcase::find(Cache::get('requestid'));
        $moodle = new Moodle();
        if ($status = $moodle->getMoodle($x='tdsv')) //Request was sucessful
        {
            //Create folders for retrived data
            $dir = new CaseStore();
            $dir->makesystemfolder(config('services.moodle-test.client_name'));

            //Store zipfile in directory
            $dir->storeZip(config('services.moodle-test.client_name'), $status);

            //Unzip
            $dir->unzip(config('services.moodle-test.client_name'));

            //Status flags
            $update->status_moodle_test = $update->status_moodle_test+100; //Temporary flag 50%
            $update->download =  $update->download+1; //Temporary finished download
        }
        else
        {
            $update->status_moodle_test = $update->status_moodle_test+0; //Unsucessful request flag 0%
        }
        //Update and save status
        $update->save();
    }
}
