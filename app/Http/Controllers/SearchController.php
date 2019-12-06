<?php

namespace App\Http\Controllers;


use App\Jobs\ProcessMoodlePlugin;
use App\Jobs\ProcessSciproDevPlugin;
use App\Services\CaseStore;
use Illuminate\Http\Request;
use App\Searchcase;
use App\Plugin\Scipro;
use App\Plugin\Moodle;

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
         * 1. Request and store form entries in cache
         * 2. Generate unique request id
         * 3. Store request data in cache
         * 4. Store initiate request data in database table
         * 5. Perform request to plugin scripts
         *
        */

        // 1. Requesting data from form
        $personnr = $request->input('personnr');
        $email = $request->input('gdpr_email');
        $userid = $request->input('gdpr_userid');

        // 2. Generate unique case -id

        if(!$record = Searchcase::latest()->first())
        {
            //Store initial request data to model
            $request = Searchcase::create([
                'case_id' => config('services.case.start'),
                'request' => $userid,
                'status_scipro_dev' => 0,
                'status_moodle_test' => 0,
                'registrar' => false,
                'download' => 0,
            ]);
            $caseid = config('services.case.start');
            //Store case_id in cache for 60min
            Cache::put('request', $caseid, 60);

            // 3. Store search in cache for 60 min
            Cache::put('search', $userid, 60);
            $id = $request->id;

            Cache::put('requestid', $id, 60);
        }
        else
        {
            $expNum = explode('-', $record->case_id);
            $nextCaseNumber = $expNum[0].'-'. (string)((int)$expNum[1]+1);
            // Request case_id
            $caseid = $nextCaseNumber;

            // 3. Store request in cache

            //Store case_id in cache for 60min
            Cache::put('request', $caseid, 60);
            //Store search in cache for 60 min
            Cache::put('search', $userid, 60);

            // 4. Store initial requestdata to model
            $request = Searchcase::create([
                'case_id' => $caseid,
                'request' => $userid,
                'status_scipro_dev' => 0,
                'status_moodle_test' => 0,
                'registrar' => false,
                'download' => 0,
            ]);
            $id = $request->id;
            //Store primary key
            Cache::put('requestid', $id, 60);
        }

        // 5. Start JobsPlugins
        //Create folders for retrived data
        $dir = new CaseStore();
        $dir->makedfolders();
        //Start Moodle job
        $moodleJob = new ProcessMoodlePlugin();
        dispatch($moodleJob);

        //Start Scipro dev job
        $scipro->auth();

        // Job end
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
