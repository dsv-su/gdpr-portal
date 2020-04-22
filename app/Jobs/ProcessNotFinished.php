<?php

namespace App\Jobs;

use App\Mail\GDPRNotifyError;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Mail;

class ProcessNotFinished implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $case;
    public function __construct($case)
    {
        $this->case = $case;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //$user = Cache::get('requester_email');
        $user = $this->case->gdpr_useremail;

        $details = [
            'title' => 'Meddelande från GDPR portalen',
            'url' => 'https://'.$this->case->gdpr_server,
            'case' => $this->case->case_id
        ];
        Mail::to($user)->send(new GDPRNotifyError($details));
    }
}
