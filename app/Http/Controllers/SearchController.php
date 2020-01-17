<?php

namespace App\Http\Controllers;


use App\Events\FinishedJobs;
use App\Jobs\ProcessMoodlePlugin;
use App\Jobs\ProcessUtbytesPlugin;
use App\Services\CaseStore;
use Illuminate\Http\Request;
use App\Searchcase;
use App\Plugin\Scipro;
use App\Plugin\Moodle;
use App\Plugin;
use App\Status;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;


class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function search(Request $request, Scipro $scipro, Searchcase $searchcase)
    {
        /*
         * 1. Request and store form entry (in cache)
         * 2. Check server/dev
         * 3. Generate unique request id
         * 4. Store request data (in cache)
         * 5. Store initiate request data in database table
         * 6. Perform request to plugin scripts
         *
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

            $id = $request->id;

            //Store primary key
            Cache::put('requestid', $id, 60);

            //Create plugin status
            $status->initPluginStatus($id);

        }

        // 6. Start JobsPlugins

        //Create folders for retrieved data
        $dir = new CaseStore();
        $dir->makedfolders();

        //Retrive case
        $case = Searchcase::find(Cache::get('requestid'));


        //**************************************************************************************************************
        //Start Moodle job
        $casestatus = Status::where([
            ['searchcase_id', '=', Cache::get('requestid')],
            ['plugin_id', '=', 1],
        ])->first();

        $moodleJob = new ProcessMoodlePlugin($case, $casestatus);
        dispatch($moodleJob);
        //**************************************************************************************************************

        //**************************************************************************************************************
        //Start Utbytes job
        $casestatus = Status::where([
            ['searchcase_id', '=', Cache::get('requestid')],
            ['plugin_id', '=', 2],
        ])->first();
        $utbytesJob = new ProcessUtbytesPlugin($case, $casestatus);
        dispatch($utbytesJob);
        //**************************************************************************************************************

        //**************************************************************************************************************
        //Start Scipro dev job
        $scipro->auth();
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
