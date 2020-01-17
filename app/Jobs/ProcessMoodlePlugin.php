<?php

namespace App\Jobs;

use App\Plugin\Moodle;
use App\Services\CaseStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMoodlePlugin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 3;
    protected $case, $status;


    public function __construct($case, $status)
    {
        $this->case = $case;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get personnummer, email and userId from case
        $pnr = $this->case->request_pnr;
        $email = $this->case->request_email;
        $uid = $this->case->request_uid;

        //Strip domainname from userid -> userid@su.se
        /* Disabled
        $searchNum = explode('@', $search);
        $search = $searchNum[0];
        */

        //Start request to Moodle
        $this->status->setDownloadStatus(25);

        $moodle = new Moodle();

        $status = $moodle->getMoodle($pnr, $email, $uid);
        if ($status == 204)
        {
            //User not found
            $this->status->setStatus(204);
            $this->status->setDownloadStatus(100);
            $this->case->setStatusFlag(3); //Successful but not found
            $this->case->setDownloadSuccess(); //Successful but not found
        }
        else if( $status == 404)
        {
            //Request denied
            $this->case->setDownloadFail(); //Download error
            $this->status->setStatus(204);
            $this->status->setDownloadStatus(100);

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
            $this->case->setStatusFlag(3);
            $this->case->setDownloadSuccess();
            $this->status->setStatus(200);
            $this->status->setDownloadStatus(100);

        }

    }
}
