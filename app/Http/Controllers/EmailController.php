<?php

namespace App\Http\Controllers;

use App\Searchcase;
use App\Mail\RegistrarSend;
use App\System;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;

class EmailController extends Controller
{
    //Send email to registrar
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
