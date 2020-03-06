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
         * 6. Get token from Toker
         ***************************************************/

        /*************************************************************************************
        //  1. Handle formdata and start a new case
        /************************************************************************************/

        //Store formdata in array
        $search_request[] = $request->input('gdpr_pnr');
        $search_request[] = $request->input('gdpr_email');
        $search_request[] = $request->input('gdpr_uid');

        /*************************************************************************************
        //  2. Check which server is running dev/methone to issue correct loginformation
        /************************************************************************************/

        if($_SERVER['SERVER_NAME'] == 'methone.dsv.su.se')
        {
            $gdpr_userid = $_SERVER['eppn'];
        }
        else {
            $gdpr_userid = 'devuser';
        }

        //Check that plugins have been loaded
        if( Plugin::count() == 0 )
        {
            dd('Please initiate script first by going bach and refreshing page!');
            return redirect()->route('home');
        }

        // New status instance
        $status = new Status();

        /*************************************************************************************
        //  3. Generate unique request id
        /************************************************************************************/

        if(!$record = Searchcase::latest()->first())
        {
            //Get system configuration
            $system = System::find(1);
            //Init a new case
            $request = new Searchcase();
            $request = $request->initCase($gdpr_userid,$search_request, $system->case_start_id);

            //Create plugin status
            $status->initPluginStatus($request->id);

        }
        else
        {
            //NextcaseId
            $expNum = explode('-', $record->case_id);
            $nextCaseNumber = $expNum[0].'-'. (string)((int)$expNum[1]+1);

            // Request case_id
            $caseid = $nextCaseNumber;

        /*************************************************************************************
        //  4. Store initiate request data to Model
        /************************************************************************************/

            $request = new Searchcase();
            $request = $request->initnewCase($gdpr_userid, $caseid, $search_request);

            //Get caseid
            $id = $request->id;

            //Init plugin status for case
            $status->initPluginStatus($id);

        }
        /*************************************************************************************
        //  5. Create folders
        /************************************************************************************/


        //Create folders for retrieved data
        $dir = new CaseStore($request);
        $dir->makedfolders();

        /*************************************************************************************
        // 6. Get toker token for plugins
        /*************************************************************************************/

        $plugins = Plugin::all();
        foreach ($plugins as $plugin)
        {
            if($plugin->auth == 'toker' or $plugin->auth == 'toker-test')
            {
                $status = Status::where([
                    ['searchcase_id','=', $request->id],
                    ['plugin_id', '=', $plugin->id],
                ])->first();
                $toker = new Toker($request, $plugin, $status);

                $toker->auth();
                exit;
            }
        }
        return redirect()->action('PluginController@run');
    }


}
