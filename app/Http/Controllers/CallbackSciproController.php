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
        //Scipro plugin_id: 2
        //Store Code from scipro-callback in cache
        Cache::put('code', $_GET['code'], 60);
        $case = Searchcase::find(Cache::get('requestid'));
        $casestatus = Status::where([
            ['searchcase_id', '=', Cache::get('requestid')],
            ['plugin_name', '=', 'scipro_dev'],
        ])->first();
        //Get scipro plugin
        $plugin = new Plugin();
        $scipro_plugin = $plugin->getPlugin('scipro_dev');

        $sciproJob = new ProcessSciproDevPlugin($case, $casestatus, $scipro_plugin);
        dispatch($sciproJob);
        //**************************************************************************************************************
        return redirect()->route('home');

    }
}
