<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessNotFinished;
use App\Services\CaseStore;
use App\Status;
use Illuminate\Http\Request;
use App\Searchcase;
use App\Plugin;
use App\Jobs\ProcessFinished;
use Illuminate\Support\Facades\Cache;


class DashboardController extends Controller
{
    public function index(Searchcase $searchcase)
    {
        //Initiate testing plugins at first boot ->To be removed
        //-----------------------------------------------
        if(!$record = Searchcase::latest()->first()) {
            if (!$plugin = Plugin::latest()->first()) {

                // Read gdpr.ini file and store id db
                $file = base_path().'/gdpr.ini';
                if (!file_exists($file)) {
                    $file = base_path().'/gdpr.ini.example';
                }
                $plugin_config = parse_ini_file($file, true);
                $plugin = new Plugin();
                foreach ($plugin_config as $config)
                {
                $plugin->newPlugin($config['client_name']);
                }

            }
        }
        //-----------------------------------------------


        $data['systems'] = Plugin::count();
        $data['cases'] = Searchcase::all();
        $data['pluginstatuses'] = Status::all();

        //Check download status
        if(!$record = Searchcase::latest()->first()) {
        }
        else {
            $plugins = Plugin::count();
            $cases = Searchcase::all();
            foreach ($cases as $case)
            {
                if($case->status_flag == 3)
                {
                    $case->download_status = ($case->plugins_processed/$plugins)*100;
                }
                else
                {
                    $case->setDownloadStatus(0);
                }
                $case->save();
            }

        }


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
        $data['pluginstatuses'] = Status::all();
        //Check download status
        if(!$record = Searchcase::latest()->first()) {
        }
        else {
            $plugins = Plugin::count();
            $cases = Searchcase::all();
            foreach ($cases as $case)
            {
                if($case->status_flag == 3)
                {
                    $case->download_status = ($case->plugins_processed/$plugins)*100;
                }
                else
                {
                    $case->setDownloadStatus(0);
                }
                $case->save();
            }

        }
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

    //-----------------------------------------------
    //
    // Developing and testing functions
    //
    //-----------------------------------------------

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

    public function phpinfo()
    {
        return phpinfo();
    }


}
