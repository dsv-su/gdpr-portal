<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPlugin;
use App\Searchcase;
use App\Status;
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
        //Loop to execute the auth method for each plugin

        foreach($plugins as $plugin)
        {
            $name = 'App\Plugin\\'. $plugin->name;
            $casestatus = Status::where([
                ['searchcase_id', '=', $case->id],
                ['plugin_name', '=', $plugin->name],
            ])->first();
            if($casestatus->auth == 0)
            {
                // Uncomment if other tokens are required
                //$plugin_instance = new $name($case, $plugin, $casestatus);
                //$plugin_instance->auth();
                // Dispatch to que
                $pluginjob = new ProcessPlugin($case, $plugin, $casestatus);
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
