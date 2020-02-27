<?php

namespace App\Http\Controllers;

use App\Services\CaseStore;
use App\System;
use Illuminate\Http\Request;
use App\Searchcase;
use App\Plugin\Scipro;
use App\Status;
use App\Plugin;
use Illuminate\Support\Facades\Cache;


class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function search(Request $request, Scipro $scipro)
    {
        /***************************************************
         * 1. Request and store formdata
         * 2. Check which server is running
         * 3. Generate unique request id
         * 4. Store request data
         * 5. Store initiate request data in database table
         * 6. Perform request to plugin scripts
         ***************************************************
        */

        // 1. Requesting data from form
        $personnr = $request->input('gdpr_pnr');
        $email = $request->input('gdpr_email');
        $userid = $request->input('gdpr_uid');

        //Store formdata in array
        $search_request[] = $request->input('gdpr_pnr');
        $search_request[] = $request->input('gdpr_email');
        $search_request[] = $request->input('gdpr_uid');

        // 2. Check server/dev
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
            dd('Please initiate script first');
            return redirect()->route('home');
        }

        // New status instance
        $status = new Status;

        // 3. Generate unique case -id

        if(!$record = Searchcase::latest()->first())
        {
            //Get system configuration
            $system = System::find(1);
            //Init a new case
            $request = new Searchcase();
            $request = $request->initCase($gdpr_userid,$search_request, $system->case_start_id);

            $caseid = $system->case_start_id;
            //Store case_id in cache for 60min
            Cache::put('request', $caseid, 7200);

            //Store search in cache for 60 min
            Cache::put('search', $userid, 7200);
            $id = $request->id;

            Cache::put('requestid', $id, 7200);

            //Create plugin status
            $status->initPluginStatus($id);

        }
        else
        {
            //NextcaseId
            $expNum = explode('-', $record->case_id);
            $nextCaseNumber = $expNum[0].'-'. (string)((int)$expNum[1]+1);

            // Request case_id
            $caseid = $nextCaseNumber;

            // 4. Store request in cache

            //Store case_id in cache for 60min
            Cache::put('request', $caseid, 7200);
            //Store search in cache for 60 min
            Cache::put('search', $userid, 7200);

            // 5. Store initial requestdata to model

            $request = new Searchcase();
            $request = $request->initnewCase($gdpr_userid, $caseid, $search_request);

            //Get caseid
            $id = $request->id;

            //(TODO -> Remove)Store in cache
            Cache::put('requestid', $id, 7200);

            //Init plugin status for case
            $status->initPluginStatus($id);

        }
        /*************************************************************************************
        // 6. Start JobsPlugins
        /*************************************************************************************
         */

        //Create folders for retrieved data
        $dir = new CaseStore($request);
        $dir->makedfolders();

        //***********************************************************************************
        //Scipro auth
        //***********************************************************************************

        //TODO-->
        $plugin = new Plugin();
        //Get scipro plugin
        $scipro_plugin = $plugin->getPlugin('scipro_dev');
        $scipro = new Scipro($request, $scipro_plugin);
        $scipro->auth();

    }


}
