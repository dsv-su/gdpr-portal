<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPlugin;
use App\Plugin;
use App\Searchcase;
use App\Status;
use App\Toker;

class CallbackController extends Controller
{
    public function callback()
    {
        //Retrive code from callback
        $code = $_GET['code'];
        //Get case
        $case = Searchcase::latest()->first();
        //Load all plugins
        $plugins = Plugin::all();
        foreach ($plugins as $plugin)
        {
            if($plugin->auth == 'toker' or $plugin->auth == 'toker-test')
            {
                $status = Status::where([
                    ['searchcase_id','=', $case->id],
                    ['plugin_id', '=', $plugin->id],
                ])->first();
                //$toker = new Toker($case, $plugin, $status);
               // dd($toker->getToken($code));

                $status->code = $code;
                //Set callback flag
                $status->callback = 1;
                $status->save();
            }
        }

        foreach ($plugins as $plugin)
        {
            if($plugin->auth == 'toker' or $plugin->auth == 'toker-test')
            {
                $status = Status::where([
                    ['searchcase_id','=', $case->id],
                    ['plugin_id', '=', $plugin->id],
                ])->first();

                $pluginjob = new ProcessPlugin($case, $plugin, $status);
                dispatch($pluginjob);
            }
        }


        return redirect()->action('PluginController@run');
    }
}
