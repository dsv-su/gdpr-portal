<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMoodlePlugin;
use App\Jobs\ProcessUtbytesPlugin;
use App\Jobs\ProcessDaisyPlugin;
use App\Services\CaseStore;
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

        // New status instance
        $status = new Status;

        // 3. Generate unique case -id

        if(!$record = Searchcase::latest()->first())
        {

            $request = new Searchcase();
            $request = $request->initCase($gdpr_userid,$search_request);

            $caseid = config('services.case.start');
            //Store case_id in cache for 60min
            Cache::put('request', $caseid, 60);

            //Store search in cache for 60 min
            Cache::put('search', $userid, 60);
            $id = $request->id;

            Cache::put('requestid', $id, 60);

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
            Cache::put('request', $caseid, 60);
            //Store search in cache for 60 min
            Cache::put('search', $userid, 60);

            // 5. Store initial requestdata to model

            $request = new Searchcase();
            $request = $request->initnewCase($gdpr_userid, $caseid, $search_request);

            //Get caseid
            $id = $request->id;

            //Store in cache
            Cache::put('requestid', $id, 60);

            //Init plugin status
            $status->initPluginStatus($id);

        }
        /*************************************************************************************
        // 6. Start JobsPlugins
        /*************************************************************************************
         */

        //Create folders for retrieved data
        $dir = new CaseStore();
        $dir->makedfolders();

        //Retrive case
        $case = Searchcase::find(Cache::get('requestid'));
        $plugin = new Plugin();

        //***********************************************************************************
        //Start Moodle job
        //***********************************************************************************
        //Get casestatus for moodle plugin

        //TODO-->
        $casestatus = Status::where([
            ['searchcase_id', '=', Cache::get('requestid')],
            ['plugin_name', '=', 'moodle_2_test'],
        ])->first();
        //Get moodle plugin
        $moodle_plugin = $plugin->getPlugin('moodle_2_test');

        //Start Moodle job
        $moodleJob = new ProcessMoodlePlugin($case, $casestatus, $moodle_plugin);
        dispatch($moodleJob);
        //-->

        //**************************************************************************************************************

        //**************************************************************************************************************
        //Start Utbytes job
        //TODO-->

        $casestatus = Status::where([
            ['searchcase_id', '=', Cache::get('requestid')],
            ['plugin_name', '=', 'utbytes'],
        ])->first();
        //Get utbytes plugin
        $utbytes_plugin = $plugin->getPlugin('utbytes');

        $utbytesJob = new ProcessUtbytesPlugin($case, $casestatus, $utbytes_plugin);
        dispatch($utbytesJob);
        //--->

        //**************************************************************************************************************
        //**************************************************************************************************************
        //Start Daisy2 job
        //TODO-->

        $casestatus = Status::where([
            ['searchcase_id', '=', Cache::get('requestid')],
            ['plugin_name', '=', 'daisy2'],
        ])->first();
        //Get daisy plugin
        $daisy_plugin = $plugin->getPlugin('daisy2');

        $daisyJob = new ProcessDaisyPlugin($case, $casestatus, $daisy_plugin);
        dispatch($daisyJob);
        //--->

        //**************************************************************************************************************
        //**************************************************************************************************************
        //Scipro auth
        //TODO-->
        //Get scipro plugin
        $scipro_plugin = $plugin->getPlugin('scipro_dev');

        $scipro->auth($scipro_plugin->auth_url, $scipro_plugin->client_id, $scipro_plugin->redirect_uri);
        //**************************************************************************************************************

        // Request end

        return redirect()->route('home');

    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
