<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GDPRExtractRequest extends Mailable
{
    use Queueable, SerializesModels;
    protected $case;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($case)
    {
        $this->case = $case;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.gdpr_system_request')
            ->with('case', $this->case);
    }
}
