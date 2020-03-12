<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index()
    {
        return view('file.home');
    }

    public function store(Request $request)
    {
        return back()->with('message', 'Your file has been submitted Successfully');
    }
}
