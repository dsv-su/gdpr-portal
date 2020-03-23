<?php

namespace App\Plugin;

use App\Mail\GDPRExtractRequest;
use Illuminate\Support\Facades\Mail;

class Email extends GenericPlugin
{
    public function getResource()
    {
        //Send email to system owner
        Mail::to($this->plugin->owner_email)
            ->queue(new GDPRExtractRequest($this->case, $this->plugin));

        return 'pending';
    }
}
