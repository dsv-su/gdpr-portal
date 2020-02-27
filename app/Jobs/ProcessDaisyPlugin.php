<?php

namespace App\Jobs;

use Exception;
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
    protected $case, $status, $plugin;

    public function __construct($case, $status, $plugin)
    {
        $this->case = $case;
        $this->status = $status;
        $this->plugin = $plugin;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Start request to Moodle
        $this->status->setProgressStatus(25);
        $this->status->setDownloadStatus(25);

        $daisy = new Daisy($this->case, $this->plugin);

        $status = $daisy->getDaisy();

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
            $dir = new CaseStore($this->case);
            $dir->makesystemfolder($this->plugin->name);

            //Store zipfile in directory
            $dir->storeZip($this->plugin->name, $status);

            //Unzip
            $dir->unzip($this->plugin->name);

            //Status flags
            $this->status->setStatus(200);
            $this->status->setProgressStatus(100);
            $this->status->setDownloadStatus(100);

        }
        $this->case->setPluginSuccess();
    }

    public function failed(Exception $exception)
    {
        //Status flags
        $this->status->setStatus(400);
        $this->status->setProgressStatus(100);
        $this->status->setDownloadStatus(0);
        $this->case->setStatusFlag(0);
    }
}
