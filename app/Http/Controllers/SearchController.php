<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Searchcase;
use App\Plugin\Scipro;
use App\Plugin\Moodle;

use Illuminate\Support\Facades\Cache;


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
         *  -if not sucessful -> update status in database table
         * 6. Store returned files on disk
         * 7. Update status in database table
         * 8. Redirect to dashboard
         *
        */

        // 1. Requesting data from form
        $personnr = $request->input('personnr');
        $email = $request->input('gdpr_email');
        $userid = $request->input('gdpr_userid');

        // 2. Generate unique case -id

        if(!$record = Searchcase::latest()->first())
        {
            //Store initial requestdata to model
            $request = Searchcase::create([
                'case_id' => '2019-0',
                'request' => $userid,
                'status' => 0,
                'registrar' => false,
                'download' => 0,
            ]);
            $caseid = '2019-0';
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

            //Store case_id in cache for 60min
            Cache::put('request', $caseid, 60);

            // 3. Store search in cache for 60 min
            Cache::put('search', $userid, 60);

            // 4. Store initial requestdata to model
            $request = Searchcase::create([
                'case_id' => $caseid,
                'request' => $userid,
                'status' => 0,
                'registrar' => false,
                'download' => 0,
            ]);
            $id = $request->id;

            Cache::put('requestid', $id, 60);
        }

//------------------------------------------------------------------------------
        // 5. Start search

        //Start GDPR request to scipro-dev
        $scipro->auth();



        //If error show status
        return redirect('/');

    }



    public function callMoodle(Moodle $moodle)
    {
        $status = $moodle->getMoodle();
        $update = Searchcase::find(Cache::get('requestid'));

        if ($status == 200) //Request was sucessful
        {
            $update->status = $update->status+50; //Temporary flag 100%
            $update->download =  $update->download+1; //Temporary finished download
        }
        else
        {
            $update->status = $update->status+0; //Unsucessful request flag 0%
        }
        $update->save();
        return redirect('/');
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
