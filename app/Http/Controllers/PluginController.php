<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPlugin;
use App\Mail\GDPRExtractRequest;
use App\Searchcase;
use App\Status;
use Illuminate\Http\Request;
use App\Plugin;
use Illuminate\Support\Facades\Mail;

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
            if( $casestatus->auth == 0 and $casestatus->auth_system == null)
            {
                // Dispatch to que
                $pluginjob = new ProcessPlugin($case, $plugin, $casestatus);
                dispatch($pluginjob);
            }
            elseif ( $casestatus->auth == 0 and $casestatus->auth_system == 'email')
            {
                //Send email
                Mail::to($plugin->owner_email)
                    ->queue(new GDPRExtractRequest($case));

                $casestatus->setProgressStatus(100);
                $casestatus->setStatus('pending');
                $case->setPluginSuccess(); //Plugin processed successful
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
