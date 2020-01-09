<?php

namespace App\Http\Controllers;

use App\Mail\MailGDPRNotify;
use Mail;

use Illuminate\Http\Request;

class EmailController extends Controller
{
    //To test email settings on server
    public function sendEmail()
    {
        $user = 'ryan@dsv.su.se';
        $details = [
            'title' => 'Testmail från GDPR Portalen',
            'url' => 'https://methone.dsv.su.se'
        ];
        Mail::to($user)->send(new MailGDPRNotify($details));

    }
}
