<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPlugin;
use App\Plugin;
use App\Searchcase;
use App\Status;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    public function callback()
    {
        $provider = 'Scipro'; // Hardcoded untill callback url is registred in toker-test

        $case = Searchcase::latest()->first();
        $plugin = Plugin::where('name', '=', $provider)->first();
        //Store Code from callback to status
        $status = Status::where([
            ['searchcase_id','=', $case->id],
            ['plugin_id', '=', $plugin->id],
        ])->first();
        $status->code = $_GET['code'];
        //Set callback flag
        $status->callback = 1;
        $status->save();

        $pluginjob = new ProcessPlugin($case, $plugin, $status);
        dispatch($pluginjob);

        return redirect()->action('PluginController@run');
    }
}
