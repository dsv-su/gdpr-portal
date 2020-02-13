<?php

namespace App\Jobs;

use Exception;
use App\Plugin\Scipro;
use App\Searchcase;
use App\Status;
use App\Services\CaseStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Zip;

class ProcessSciproDevPlugin implements ShouldQueue
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
        //Start GDPR request to scipro-dev
        //-------------TODO--------------------------------------
        $scipro = new Scipro(Cache::get('code'));

        //-------------------------------------------------------
        //Start request to Sciprodev

        //Initiate Status flags
        $this->status->setProgressStatus(25);
        $this->status->setDownloadStatus(25);
        $status = $scipro->gettoken($this->plugin->base_uri, $this->plugin->client_id, $this->plugin->client_secret, $this->plugin->redirect_uri, $this->plugin->endpoint_url);
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
        else if( $status == 400)
        {
            //*********************************************************************
            //Request denied
            //*********************************************************************
            //Status flags
            $this->status->setStatus(400);
            $this->status->setProgressStatus(100);
            $this->status->setDownloadStatus(0);
            $this->case->setStatusFlag(0);
        }
        else
        {
            //********************************************************************
            //Sucessfull download
            //********************************************************************

            //Create folders for retrived data
            $dir = new CaseStore();
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
