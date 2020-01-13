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
        $update->download_scipro_dev = 25;

        $pluginstatus->download_status = 25;
        //Update and save initiate status
        $update->save();
        $pluginstatus->save();

        $status = $scipro->gettoken();
        if ($status == 204)
        {
            //User not found
            $update->status_scipro_dev = 204;
            $update->download_scipro_dev = 100;

            $pluginstatus->status = 204; //Status to status
            $pluginstatus->download_status = 100; //Status to status

        }
        else if( $status == 400)
        {
            //Request denied
            $update->status_scipro_dev = 400;
            $update->download_scipro_dev = 100;

            $pluginstatus->status = 400; //Status to status
            $pluginstatus->download_status = 100; //Status to status

            $update->status_flag = 0; //Set flag to Error
        }
        else
        {

            //Create folders for retrived data
            $dir = new CaseStore();
            //$dir->makedfolders(config('services.scipro-dev.client_name'));

            //Store zipfile in directory
            $dir->storeZip(config('services.scipro-dev.client_name'), $status);

            //Unzip
            $dir->unzip(config('services.scipro-dev.client_name'));

            //Status flags
            $update->status_scipro_dev = 200;
            $update->download_scipro_dev = 100;

            $pluginstatus->status = 200; //Status to status
            $pluginstatus->download_status = 100; //Status to status

            $update->download =  $update->download+1; // Finished download
        }
        //Update and save status
        $update->save();
        $pluginstatus->save();

    }
}
