<?php

namespace App\Http\Controllers;

use App\Plugin;
use App\Searchcase;
use App\Status;
use App\System;
use App\Toker;

class CallbackController extends Controller
{
    /****************************
     * @return \Illuminate\Http\RedirectResponse
     *
     * Handles Callback from Toker and stores token
     *
     */

    public function callback()
    {
        //Retrive code from callback
        $code = $_GET['code'];
        //Get latest case
        $case = Searchcase::latest()->first();
        //Load all plugins
        $plugins = Plugin::all();
        //Get system config
        $system = System::find(1);
        //Retrive token
        $toker = new Toker($system);
        $token = $toker->getToken($code);
        //Persist token
        foreach ($plugins as $plugin)
        {
                $status = Status::where([
                    ['searchcase_id','=', $case->id],
                    ['plugin_id', '=', $plugin->id],
                ])->first();
                // Save token to plugin
                $status->token = $token;
                //Set callback flag
                $status->callback = 1;
                $status->save();
        }

        return redirect()->action('PluginController@run');
    }
}
