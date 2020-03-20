<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class GDPRExtractRequest extends Mailable
{
    use Queueable, SerializesModels;
    protected $case, $plugin;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($case, $plugin)
    {
        $this->case = $case;
        $this->plugin = $plugin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $link = URL::signedRoute('upload', ['case_id' => $this->case->case_id, 'system' => $this->plugin->name]);
        //$link = 'https://methone.dsv.su.se/upload/'.$this->case->case_id.'/'. $this->plugin->name;
        return $this->view('emails.gdpr_system_request')
                     ->with([
                        'case' => $this->case,
                        'link'=> $link,
                        ]);

    }
}
