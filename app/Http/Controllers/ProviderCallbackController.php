<?php

namespace App\Http\Controllers;

use App\Plugin;
use App\Searchcase;
use App\Status;

class ProviderCallbackController extends Controller
{
    public function callbackScipro(Searchcase $searchcase)
    {
        //**************************************************************************************************************
        $case = Searchcase::latest()->first();
        $plugin = Plugin::where('name', '=', 'scipro_dev')->first();
        //Store Code from scipro-callback in cache
        $status = Status::where([
            ['searchcase_id','=', $case->id],
            ['plugin_id', '=', $plugin->id],
        ])->first();
        $status->code = $_GET['code'];
        $status->save();

        return redirect()->action('PluginController@run');

        //**************************************************************************************************************
    }
}
