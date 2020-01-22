<?php

namespace App\Http\Controllers;

use App\Searchcase;
use App\Mail\RegistrarSend;
use Mail;

use Illuminate\Http\Request;

class EmailController extends Controller
{
    //To test email settings on server
    public function sendEmail($id)
    {
        $registrar = config('services.registrator.epost');
        $case = Searchcase::find($id);

        //Register sent date
        $case->setRegistrar();

        //return new RegistrarSend($case);
        Mail::to($registrar)
              ->queue(new RegistrarSend($case));

        return redirect()->route('home');

    }
}
