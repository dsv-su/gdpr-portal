<?php

namespace App\Http\Controllers;

use App\Services\CaseStore;
use Illuminate\Http\Request;
use App\Searchcase;


class DashboardController extends Controller
{
    public function index(Searchcase $searchcase)
    {
        $data['cases'] = Searchcase::all();
        if($_SERVER['SERVER_NAME'] == 'methone.dsv.su.se')
        {
            $data['gdpr_user'] = $_SERVER['displayName'];
        }
        else {
            $data['gdpr_user'] = 'Ryan Dias';
        }
        return view('home.dashboard', $data);
    }

    public function status(Searchcase $searchcase)
    {
        $data['cases'] = Searchcase::all();
        return view('home.status', $data);
    }

    public function download($id)
    {
        //Create zip of retried files and folder
        $zipdown = new CaseStore();
        $zipdown->makezip($id);
        return $zipdown->download($id);
    }
    public function delete($id)
    {
        //Delete zip and retrived files and folder
        $zip = new CaseStore();
        $zip->delete_case($id);

        return redirect()->route('home');
    }
    public function dev_delete($id)
    {
        //Delete zip and retrived files and folder
        $zip = new CaseStore();
        $zip->dev_delete_case($id);

        return redirect()->route('home');
    }
    public function dev()
    {
        //Delete zip and retrived files and folder
        $zip = new CaseStore();
        $zip->dev_delete();

        return redirect()->route('home');
    }

    public function test()
    {
        return $_SERVER;
    }
    public function test1()
    {
        if($_SERVER['SERVER_NAME'] == 'methone.dsv.su.se')
        {
            return $_SERVER['displayName'];
        }

    }

    public function phpinfo()
    {
        return phpinfo();
    }


}
