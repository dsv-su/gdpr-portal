<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrarSend extends Mailable
{
    use Queueable, SerializesModels;
    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //Set public folder
        $public_dir = public_path().'/storage/'.$this->details->case_id.'/';
        //Set filename
        $zipFileName = $this->details->case_id.'.zip';
        // Set Header
        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );
        $filetopath = $public_dir.$zipFileName;
        return $this->view('emails.registrar')
            ->with('details', $this->details)
            ->attach($filetopath, [
                'as' => $zipFileName,
                'mime' => 'application/zip'
            ]);
    }
}
