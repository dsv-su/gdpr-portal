<?php

namespace App\Http\Controllers;

use App\Searchcase;
use App\Mail\RegistrarSend;
use App\System;
use Mail;

use Illuminate\Http\Request;

class EmailController extends Controller
{
    //To test email settings on server
    public function sendEmail($id)
    {
        $system = System::find(1);
        $registrar = $system->registrator;
        $case = Searchcase::find($id);

        //Register sent date
        $case->setRegistrar();

        //return new RegistrarSend($case);
        Mail::to($registrar)
              ->queue(new RegistrarSend($case));

        return redirect()->route('home');

    }
}
