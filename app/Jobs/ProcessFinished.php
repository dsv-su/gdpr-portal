<?php

namespace App\Jobs;

use App\Mail\MailGDPRNotify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Mail;

class ProcessFinished implements ShouldQueue
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

        $user = Cache::get('requester_email');

        $details = [
            'title' => 'Message from the GDPR portal',
            'url' => 'https://methone.dsv.su.se',
            'case' => Cache::get('request')
        ];
        Mail::to($user)->send(new MailGDPRNotify($details));
    }
}
