<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSciproDevPlugin;
use App\Plugin\Scipro;
use App\Searchcase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CallbackSciproController extends Controller
{
    public function callbackScipro(Searchcase $searchcase)
    {
        //Store code from callback in cache
        Cache::put('code', $_GET['code'], 60);

        //Start Scipro dev job
        $sciproJob = new ProcessSciproDevPlugin();
        dispatch($sciproJob);
        // Job end
        return redirect()->route('home');




    }
}
