<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Searchcase;

class DashboardController extends Controller
{
    public function index(Searchcase $searchcase)
    {
        $data['cases'] = Searchcase::all();
        return view('home.dashboard', $data);
    }
}
