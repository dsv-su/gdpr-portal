<?php

namespace App\Http\Controllers;

use App\Services\CaseStore;
use App\Services\ConfigurationHandler;
use App\Status;
use App\Searchcase;
use App\Plugin;


class DashboardController extends Controller
{
    public function index(Searchcase $searchcase)
    {
        //Initiate testing plugins at first boot
        //-----------------------------------------------
        if(!$record = Searchcase::latest()->first()) {
            if (!$plugin = Plugin::latest()->first()) {
                //Load system and plugins configuration
                $init = new ConfigurationHandler();
                $init->handle_plugins();
                $init->system();
                $collapse = 0;
            }
        }
        //-----------------------------------------------

        $data['systems'] = Plugin::count();
        $data['cases'] = Searchcase::all();
        $data['plugins'] = Plugin::all();
        $data['pluginstatuses'] = Status::all();

        //TODO -> This should be reworked <- Check download status
        if(!$record = Searchcase::latest()->first()) {
            $collapse = 0;
            $data['init'] = 0;
        }
        else {
            $collapse = $record->progress;
            $plugins = Plugin::count();
            $cases = Searchcase::all();
            if (!empty($cases))
                $data['init'] = 1;
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
        }
        else {
            $data['gdpr_user'] = 'Ryan Dias';
        }

        $data['collapse'] = $collapse;

        return view('home.dashboard', $data);
    }

    public function status(Searchcase $searchcase)
    {
        $data['cases'] = Searchcase::all();
        $data['pluginstatuses'] = Status::all();
        //TODO -> Check download status
        if(!$record = Searchcase::latest()->first()) {
            $data['init'] = 0;
        }
        else {
            $collapse = $record->progress;
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
        $data['collapse'] = $collapse;
        return view('home.status', $data);
    }

    public function download($id)
    {
        //Create zip of retried files and folder
        $case = Searchcase::find($id);
        $zipdown = new CaseStore($case);
        $zipdown->makezip($id);
        return $zipdown->download($id);
    }

    public function delete($id)
    {
        //Delete zip and retrived files and folder
        $case = Searchcase::find($id);
        $zip = new CaseStore($case);
        $zip->delete_case($id);

        return redirect()->route('home');
    }
    public function override($id)
    {
        $case = Searchcase::find($id);
        $case->status_flag = 3;
        $case->save();
        return redirect()->back();
    }
    //-----------------------------------------------
    //
    // Developing and testing functions
    //
    //-----------------------------------------------

    public function dev_delete($id)
    {
        //Delete zip and retrived files and folder
        $case = Searchcase::find($id);
        $zip = new CaseStore($case);
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
