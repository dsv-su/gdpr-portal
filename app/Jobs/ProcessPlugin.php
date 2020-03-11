<?php

namespace App\Jobs;

use Exception;
use App\Services\CaseStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

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

    public function __construct($case, $plugin, $status)
    {
        $this->case = $case;
        $this->plugin = $plugin;
        $this->status = $status;
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

        $system = 'App\Plugin\\'. $this->plugin->name;
        $system_instance = new $system($this->case, $this->plugin, $this->status);
        //For classname = methodname
        //$getPlugin = 'get'. $this->plugin->name;
        //$response = $system_instance->$getPlugin();
        $response = $system_instance->getResource();

        if ( $response == 'not_found')
        {
            //**********************************************************************
            //User not found
            //**********************************************************************
            // Status flags
            //TODO Move this to new class
            Storage::disk('public')->put($this->case->case_id . '/raw/'.$this->plugin->name. '/Felmeddelande' . '.txt', 'User not found - system reported 204');
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
            //TODO Move this to new class
            Storage::disk('public')->put($this->case->case_id . '/raw/'.$this->plugin->name. '/Felmeddelande' . '.txt', 'System Error - system reported 400 or 404');

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
            //TODO Move this to new class
            Storage::disk('public')->put($this->case->case_id . '/raw/'.$this->plugin->name. '/Felmeddelande' . '.txt', 'Mismatch - there is a mismatch in the request, please check the identity of the searched person');

            $this->case->setStatusFlag(0); //Download error
            $this->status->setStatus('mismatch'); // 404
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
                $dir = new CaseStore($this->case);
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
