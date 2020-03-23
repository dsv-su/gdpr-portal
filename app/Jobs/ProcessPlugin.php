<?php

namespace App\Jobs;

use Exception;
use App\Services\CaseStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPlugin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $tries = 3;

    public $timeout = 7200;
    protected $case, $plugin, $status;

    public function __construct($case, $plugin, $status, $system)
    {
        $this->case = $case;
        $this->plugin = $plugin;
        $this->status = $status;
        $this->system = $system;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Start request to Plugin
        $this->status->setProgressStatus(100);
        $this->status->setDownloadStatus(5);

        //Prepare reporting
        $dir = new CaseStore($this->case);

        $system = 'App\Plugin\\'. $this->plugin->plugin;
        $system_instance = new $system($this->case, $this->plugin, $this->status);
        echo $system;
        $response = $system_instance->getResource();

        if ( $response == 'not_found')
        {
            //**********************************************************************
            //User not found
            //**********************************************************************
            // Status flags
            $dir->errorMessage($this->plugin, $response);
            $this->status->setStatus('not_found'); //204
            $this->status->setProgressStatus(100);
            $this->status->setDownloadStatus(0);
        }
        else if(  $response == 'error')
        {
            //*********************************************************************
            //Request Error
            //*********************************************************************
            //Status flags
            $dir->errorMessage($this->plugin, $response);
            $this->case->setStatusFlag(0); //Download error
            $this->status->setStatus('error'); // 404
            $this->status->setProgressStatus(100); //Progressbar
            $this->status->setDownloadStatus(0);

        }
        else if(  $response == 'mismatch')
        {
            //*********************************************************************
            //Request Mismatch
            //*********************************************************************

            $dir->errorMessage($this->plugin, $response);
            $this->case->setStatusFlag(0); //Download error
            $this->status->setStatus('mismatch'); // 409
            $this->status->setProgressStatus(100); //Progressbar
            $this->status->setDownloadStatus(0);
        }
        else if(  $response == 'pending')
        {
            //*********************************************************************
            //Request Pending
            //*********************************************************************

            //$dir->errorMessage($this->plugin, $response);
            //$this->case->setStatusFlag(0); //Download error
            $this->status->setStatus('pending'); // 300
            $this->status->setProgressStatus(100); //Progressbar
            $this->status->setDownloadStatus(0);
        }
        else
        {
            //********************************************************************
            //Sucessfull download
            //********************************************************************

            //Create folders for retrived data
            if( $this->status->zip == 1 ) {

                $dir->makesystemfolder($this->plugin->name);

                //Store zipfile in directory
                $dir->storeZip($this->plugin->name, $response);

                //Unzip
                $dir->unzip($this->plugin->name);
                $this->status->setStatus('ok'); // 200
                $this->status->setProgressStatus(100);
                $this->status->setDownloadStatus(100);
            }
            else
            {

                $this->status->setStatus('ok'); //$response
                $this->status->setProgressStatus(100);
                //$this->status->setDownloadStatus(0); //Moved to plugin
            }
            //Status flags



        }
        $this->case->setPluginSuccess(); //Plugin processed successful

    }

    public function failed(Exception $exception)
    {
        //Status flags
        $this->status->setStatus($response);
        $this->status->setProgressStatus(100);
        $this->status->setDownloadStatus(0);
        $this->case->setStatusFlag(0);
    }

}
