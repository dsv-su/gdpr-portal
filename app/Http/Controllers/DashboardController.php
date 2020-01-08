<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessNotFinished;
use App\Services\CaseStore;
use Illuminate\Http\Request;
use App\Searchcase;
use App\Jobs\ProcessFinished;
use Illuminate\Support\Facades\Cache;


class DashboardController extends Controller
{
    public function index(Searchcase $searchcase)
    {
        $data['cases'] = Searchcase::all();
        if($_SERVER['SERVER_NAME'] == 'methone.dsv.su.se')
        {
            $data['gdpr_user'] = $_SERVER['displayName'];
            //TODO change cache to eloquent
            Cache::put('requester_email', $_SERVER['mail'], 60);
        }
        else {
            $data['gdpr_user'] = 'Ryan Dias';
            Cache::put('requester_email', 'ryan@dsv.su.se', 60);
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
    cd
    public function testview()
    {
        $data['cases'] = Searchcase::all();
        if($_SERVER['SERVER_NAME'] == 'methone.dsv.su.se')
        {
            $data['gdpr_user'] = $_SERVER['displayName'];
            //TODO change cache to eloquent
            Cache::put('requester_email', $_SERVER['mail'], 60);
        }
        else {
            $data['gdpr_user'] = 'Ryan Dias';
            Cache::put('requester_email', 'ryan@dsv.su.se', 60);
        }
        return view('home.test_dashboard', $data);
    }

    public function phpinfo()
    {
        return phpinfo();
    }


}
