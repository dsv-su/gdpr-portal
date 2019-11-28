<?php

namespace App\Jobs;

use App\Plugin\Scipro;
use App\Searchcase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProcessSciproDevPlugin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
            Storage::makeDirectory('/public/'.Cache::get('request'));
            Storage::disk('public')->put(Cache::get('request').'/'.Cache::get('request').'_scipro-dev.zip', $status);
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
