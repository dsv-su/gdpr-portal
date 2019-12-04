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
use Illuminate\Support\Facades\Storage;
use Zip;

class ProcessSciproDevPlugin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $x;

    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Start GDPR request to scipro-dev
        $scipro = new Scipro(Cache::get('code'));
        $update = Searchcase::find(Cache::get('requestid'));
        if ($status = $scipro->gettoken()) //Request was sucessful
        {
            //Create folders for retrived data
            $dir = new CaseStore();
            $dir->makedfolders(config('services.scipro-dev.client_name'));

            //Store zipfile in directory
            $dir->storeZip(config('services.scipro-dev.client_name'), $status);

            //Unzip
            $dir->unzip(config('services.scipro-dev.client_name'));

            $update->status = $update->status+100; //Temporary flag 50%
            $update->download =  $update->download+2; //Temporary finished download
        }
        else
        {
            $update->status = $update->status+0; //Unsucessful request flag 0%
        }
        $update->save();
    }
}
