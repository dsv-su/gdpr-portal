<?php

namespace App\Jobs;

use App\Plugin\Scipro;
use App\Searchcase;
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
        //TODO Use of Cache -> change to eloquent
        $update = Searchcase::find(Cache::get('requestid'));
        //Start request to Sciprodev
        $update->download_scipro_dev = 25;
        //Update and save initiate status
        $update->save();

        $status = $scipro->gettoken();
        if ($status == 204)
        {
            //User not found
            $update->status_scipro_dev = 204;
            $update->download_scipro_dev = 100;
        }
        else if( $status == 400)
        {
            //Request denied
            $update->status_scipro_dev = 400;
            $update->download_scipro_dev = 100;
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
            $update->download =  $update->download+1; //Temporary finished download
        }
        //Update and save status
        $update->save();
    }
}
