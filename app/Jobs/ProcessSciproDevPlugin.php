<?php

namespace App\Jobs;

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


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Start GDPR request to scipro-dev
        $scipro = new Scipro(Cache::get('code'));
        //-------------TODO--------------------------------------
        //Scipro plugin_id: 2
        $update = Searchcase::find(Cache::get('requestid'));
        $pluginstatus = Status::where([
            ['searchcase_id', '=', Cache::get('requestid')],
            ['plugin_id', '=', 2],
        ])->first();
        //-------------------------------------------------------
        //Start request to Sciprodev

        //Initiate Status flags
        $pluginstatus->setDownloadStatus(25);

        $status = $scipro->gettoken();
        if ($status == 204)
        {
            //User not found
            $pluginstatus->setStatus(204);
            $update->setStatusFlag(3); //Successful but not found
            $pluginstatus->setDownloadStatus(100);
            $update->setDownloadSuccess();
        }
        else if( $status == 400)
        {
            //Request denied
            $pluginstatus->setStatus(400);
            $pluginstatus->setDownloadStatus(100);
            $update->setDownloadFail();
            $update->setStatusFlag(0);
        }
        else
        {
            //********************************************************************
            //Sucessfull download
            //********************************************************************

            //Create folders for retrived data
            $dir = new CaseStore();

            //Store zipfile in directory
            $dir->storeZip(config('services.scipro-dev.client_name'), $status);

            //Unzip
            $dir->unzip(config('services.scipro-dev.client_name'));

            //Status flags
            $update->setStatusFlag(3);
            $pluginstatus->setStatus(200);
            $pluginstatus->setDownloadStatus(100);
            $update->setDownloadSuccess();
        }

    }
}
