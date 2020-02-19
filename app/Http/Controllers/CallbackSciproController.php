<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSciproDevPlugin;
use App\Plugin;
use App\Searchcase;
use App\Status;
use Illuminate\Support\Facades\Cache;

class CallbackSciproController extends Controller
{
    public function callbackScipro(Searchcase $searchcase)
    {
        //**************************************************************************************************************
        //Store Code from scipro-callback in cache
        //Cache::put('code', $_GET['code'], 144000);
        $scipro_plugin = Plugin::where('name', '=', 'scipro_dev')->first();
        $scipro_plugin->status = $_GET['code'];
        $scipro_plugin->save();
        return redirect()->action('PluginController@run');

        //**************************************************************************************************************
       //return redirect()->route('home');

    }
}
