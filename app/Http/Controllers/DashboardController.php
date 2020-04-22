<?php

namespace App\Http\Controllers;

use App\Services\CaseStore;
use App\Services\ConfigurationHandler;
use App\Status;
use App\Searchcase;
use App\Plugin;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function login()
    {
        return redirect('/');
    }

    public function index(Searchcase $searchcase)
    {
        //Initiate plugins at first boot
        if(!$record = Searchcase::latest()->first()) {
            if (!$plugin = Plugin::latest()->first()) {
                //Load system and plugins configuration
                $init = new ConfigurationHandler();
                $init->handle_plugins();
                $init->system();
                $collapse = 0;
            }
        }

        //Check if system and plugins have been modified or added
        $init = new ConfigurationHandler();
        $init->check_system();
        $init->reset_plugins();

        //Load data for view

        $data['systems'] = Plugin::count();
        $data['cases'] = Searchcase::all();
        $data['plugins'] = Plugin::all();
        $data['pluginstatuses'] = Status::all();
        $data['system_name'] = DB::table('plugins')->distinct()->pluck('plugin');


        //Check download status
        if(!$record = Searchcase::latest()->first()) {
            $collapse = 0;
            $data['init'] = 0;
        }
        else {
            $collapse = $record->progress;
            $plugins = Plugin::count();
            $cases = Searchcase::all();
            if (!empty($cases))
            {
                $data['init'] = 1;
                $collapse = 1;
            }

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

        // If the environment is local
        if(app()->environment('local'))
        {
            $data['gdpr_user'] = 'Testuser';
        }
        else {

            $data['gdpr_user'] = $_SERVER['displayName'];
        }
        // If the enviroment is in debug=true
        if(config('app.debug') == true)
        {
            $data['debug'] = true;
        }
        else $data['debug'] = false;

        $data['collapse'] = $collapse;


        return view('home.dashboard', $data);
    }

    public function status(Searchcase $searchcase)
    {
        $data['cases'] = Searchcase::all();
        $data['pluginstatuses'] = Status::all();
        //Check download status
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
        // If the enviroment is in debug=true
        if(config('app.debug') == true)
        {
            $data['debug'] = true;
        }
        else $data['debug'] = false;

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
    // Developing and testing functions when debug=true
    //
    //-----------------------------------------------

    public function dev_delete($id)
    {
        //Delete zip and retrived files and folder
        if(config('app.debug') == true)
        {
            $case = Searchcase::find($id);
            $zip = new CaseStore($case);
            $zip->dev_delete_case($id);

            return redirect()->route('home');
        }
        else return redirect()->back();

    }

    public function dev()
    {
        //Delete zip and retrived files and folder
        if(config('app.debug') == true)
        {
            $case = new Searchcase();
            $zip = new CaseStore($case);
            $zip->dev_delete();

            return redirect()->route('home');
        }
        else return redirect()->back();
    }

    public function test()
    {
        if(config('app.debug') == true)
        {
            return $_SERVER;
        }
        else return redirect()->back();
    }

    public function phpinfo()
    {
        if(config('app.debug') == true)
        {
            return phpinfo();
        }
        else return redirect()->back();
    }


}
