<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDaisyPlugin;
use App\Jobs\ProcessMoodlePlugin;
use App\Jobs\ProcessOtrsPlugin;
use App\Jobs\ProcessSciproDevPlugin;
use App\Jobs\ProcessUtbytesPlugin;
use App\Searchcase;
use App\Status;
use Illuminate\Http\Request;
use App\Plugin;
use Illuminate\Support\Facades\Cache;

class PluginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function run()
    {
        $case = Searchcase::latest()->first();

        $plugin = new Plugin();
        //***********************************************************************************
        //Start Scipro job
        //***********************************************************************************
        //Get casestatus for scipro plugin

        $scipro_plugin = $plugin->getPlugin('scipro_dev');

        $casestatus = Status::where([
            ['searchcase_id', '=', $case->id],
            ['plugin_name', '=', 'scipro_dev'],
        ])->first();

        $sciproJob = new ProcessSciproDevPlugin($case, $casestatus, $scipro_plugin);
        dispatch($sciproJob);
        //***********************************************************************************
        //Start Moodle job
        //***********************************************************************************
        //Get casestatus for moodle plugin

        //TODO-->
        $casestatus = Status::where([
            ['searchcase_id', '=', $case->id],
            ['plugin_name', '=', 'moodle_2_test'],
        ])->first();
        //Get moodle plugin
        $moodle_plugin = $plugin->getPlugin('moodle_2_test');

        //Start Moodle job
        $moodleJob = new ProcessMoodlePlugin($case, $casestatus, $moodle_plugin);
        dispatch($moodleJob);
        //-->

        //**************************************************************************************************************
        //Start Utbytes job
        //**************************************************************************************************************

        //TODO-->

        $casestatus = Status::where([
            ['searchcase_id', '=', $case->id],
            ['plugin_name', '=', 'utbytes'],
        ])->first();
        //Get utbytes plugin
        $utbytes_plugin = $plugin->getPlugin('utbytes');

        $utbytesJob = new ProcessUtbytesPlugin($case, $casestatus, $utbytes_plugin);
        dispatch($utbytesJob);
        //--->

        //**************************************************************************************************************
        //Start Daisy2 job
        //**************************************************************************************************************

        //TODO-->

        $casestatus = Status::where([
            ['searchcase_id', '=', $case->id],
            ['plugin_name', '=', 'daisy2'],
        ])->first();
        //Get daisy plugin
        $daisy_plugin = $plugin->getPlugin('daisy2');

        $daisyJob = new ProcessDaisyPlugin($case, $casestatus, $daisy_plugin);
        dispatch($daisyJob);
        //--->

        //*************************************************************************************
        //Start Otrs job
        //*************************************************************************************

        //TODO-->

        $casestatus = Status::where([
            ['searchcase_id', '=', $case->id],
            ['plugin_name', '=', 'otrs'],
        ])->first();
        //Get daisy plugin
        $otrs_plugin = $plugin->getPlugin('otrs');

        $otrsJob = new ProcessOtrsPlugin($case, $casestatus, $otrs_plugin);
        dispatch($otrsJob);
        //--->
        //***********************************************************************************
        return redirect()->route('home');

    }

    public function index()
    {
        $data['plugins'] = Plugin::all();

        return view ('plugin.config', $data);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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
        $plugin = Plugin::find($id);

        $plugin->name = request('pluginname');
        $plugin->client_id = request('plugin_client_id');
        $plugin->status = request('pluginstatus');

        $plugin->save();

        return redirect()->route('plugin');
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
