<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPlugin;
use App\Searchcase;
use App\Status;
use App\System;
use Illuminate\Http\Request;
use App\Plugin;

class PluginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function run()
    {
        //Get the caseid
        $case = Searchcase::latest()->first();

        //Get the plugins
        $plugins = Plugin::all();

        //Get system settings
        $system = System::find(1);
        //Loop to execute the auth method for each plugin

        foreach($plugins as $plugin)
        {
            $name = 'App\Plugin\\'. $plugin->name;
            $casestatus = Status::where([
                ['searchcase_id', '=', $case->id],
                ['plugin_name', '=', $plugin->name],
            ])->first();
            if( $casestatus->auth == 0 )
            {
                // Dispatch to que
                $pluginjob = new ProcessPlugin($case, $plugin, $casestatus, $system);
                dispatch($pluginjob);
            }

        }
        return redirect()->route('home');

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
