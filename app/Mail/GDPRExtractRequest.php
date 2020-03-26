<?php

namespace App\Mail;

use App\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

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
        $salt = Str::random(32);
        $hash = Hash::make($this->case->case_id.$this->plugin->name.$salt);
        //Remove forward- and backslashes
        $hash = preg_replace('/\\\\/', '', $hash);
        $hash = preg_replace('/\\//', '', $hash);
        $upload = new Upload();
        $upload->case_id = $this->case->case_id;
        $upload->system = $this->plugin->name;
        $upload->hash = $hash;
        $upload->save();
        //TODO->
        $link = 'https://methone.dsv.su.se/upload/'. $hash;

        return $this->text('emails.gdpr_system_request_text')
                     ->with([
                        'case' => $this->case,
                        'link'=> $link,
                        ]);

    }
}
