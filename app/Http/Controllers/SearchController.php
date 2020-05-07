<?php

namespace App\Http\Controllers;

use App\Services\CaseStore;
use App\System;
use App\Toker;
use Illuminate\Http\Request;
use App\Searchcase;
use App\Status;
use App\Plugin;


class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */

    public function search(Request $request)
    {
        /***************************************************
         * 1. Handle formdata and start a new case
         * 2. Check which server is running dev/methone to issue correct loginformation
         * 3. Generate unique request id
         * 4. Store initiate request data to Model
         * 5. Create folders for case
         * 6 Check which plugins to run
         * 7. Get token from Toker
         ***************************************************/

        /*************************************************************************************
        //  1. Handle formdata and start a new case
        /************************************************************************************/

        //Store formdata in array
        $search_request[] = $request->input('gdpr_pnr');
        $search_request[] = $request->input('gdpr_email');
        $search_request[] = $request->input('gdpr_uid');

        /*************************************************************************************
        //  2. Instances
        /************************************************************************************/

        // Plugins
        $plugins = Plugin::all();
        // New status instance
        $status = new Status();
        //Get system configuration
        $system = System::find(1);
        //Init a new case


        foreach ($plugins as $plugin)
        {
            $plugin_activate[] = $request->input($plugin->name);
        }

        /*************************************************************************************
        //  3. Check which server is running dev/production to issue correct loginformation
        /************************************************************************************/

        if(app()->environment('local'))
        {
            $gdpr_userid = 'devuser';
        }
        else {

            $gdpr_userid = $_SERVER['eppn'];
        }

        //Check that plugins have been loaded
        if( Plugin::count() == 0 )
        {
            dd('Please initiate script first by going back and refreshing page!'); //Only on first time boot
            return redirect()->route('home');
        }

        /*************************************************************************************
        //  4. Generate unique request id
        /************************************************************************************/

        if(!$record = Searchcase::latest()->first())
        {
            $request = new Searchcase();
            $request = $request->initCase($gdpr_userid,$search_request, 1 ); //$system->case_start_id

            //Create plugin status
            $status->initPluginStatus($request->id);
        }
        else
        {
            //NextcaseId
            /***
             * For case_id format year-no
             */
            /*
            $expNum = explode('-', $record->case_id);
            $nextCaseNumber = $expNum[0].'-'. (string)((int)$expNum[1]+1);
            */
            /** Else standard format */
            $nextCaseNumber = $record->id + 1;

            // Request case_id
            $caseid = $nextCaseNumber;

        /*************************************************************************************
        //  5. Store initiate request data to Model
        /************************************************************************************/
            $request = new Searchcase();
            $request = $request->initnewCase($gdpr_userid, $caseid, $search_request);

            //Get caseid
            $id = $request->id;

            //Init plugin status for case
            $status->initPluginStatus($id);

        }
        /*************************************************************************************
        //  6. Create folders
        /************************************************************************************/

        //Create folders for retrieved data
        $dir = new CaseStore($request);
        $dir->makedfolders();

        /*************************************************************************************
        //  7. Check which plugins to run
        /************************************************************************************/

        $x = 0;
        $plugindisabled = 0;
        foreach ($plugins as $plugin)
        {
            $status = Status::where([
                ['searchcase_id','=', $request->id],
                ['plugin_id', '=', $plugin->id],
                ])->first();
                if($plugin_activate[$x] == 1)
                {
                    $status->auth = $plugin_activate[$x];
                    $request->setPluginSuccess(); //Plugin processed successful
                    $status->setStatus('not_selected');
                    $status->save();
                    $request->save();
                    $plugindisabled++;
                }
                $x++;
        }

        //Check to see if all plugins have been deactivated
        $count = Plugin::count();
        if ($count == $plugindisabled)
        {
            // Set status flags
            $request->setProgress(0); //Kill progress flag
            $request->setStatusFlag(2);

            // Remove case folders

            $dir->delete_empty_case($request->id);
        }

        /*************************************************************************************
        // 8. Get toker token for plugins
        /*************************************************************************************/

        $toker = new Toker($system);
        $toker->auth();
        exit;

        return redirect()->action('PluginController@run');
    }


}
