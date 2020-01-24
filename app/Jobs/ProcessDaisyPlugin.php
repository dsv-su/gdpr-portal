<?php

namespace App\Jobs;

use App\Plugin\Daisy;
use App\Services\CaseStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDaisyPlugin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
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
        $this->status->setProgressStatus(25);
        $this->status->setDownloadStatus(25);

        $utbytes = new Daisy();

        $status = $utbytes->getDaisy($pnr, $email, $uid);
        if ($status == 204)
        {
            //**********************************************************************
            //User not found
            //**********************************************************************
            // Status flags
            $this->status->setStatus(204);
            $this->status->setProgressStatus(100);
            $this->status->setDownloadStatus(0);
        }
        else if( $status == 404)
        {
            //*********************************************************************
            //Request denied
            //*********************************************************************
            //Status flags
            $this->case->setStatusFlag(0); //Download error
            $this->status->setStatus(404);
            $this->status->setProgressStatus(100);
            $this->status->setDownloadStatus(0);

        }
        else
        {
            //********************************************************************
            //Sucessfull download
            //********************************************************************

            //Create folders for retrived data
            $dir = new CaseStore();
            $dir->makesystemfolder(config('services.daisy.client_name'));

            //Store zipfile in directory
            $dir->storeZip(config('services.daisy.client_name'), $status);

            //Unzip
            $dir->unzip(config('services.daisy.client_name'));

            //Status flags
            $this->status->setStatus(200);
            $this->status->setProgressStatus(100);
            $this->status->setDownloadStatus(100);

        }
        $this->case->setPluginSuccess();
    }
}
