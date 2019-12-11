<?php

namespace App\Http\Controllers;

use App\Plugin\Scipro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TestController extends Controller
{
    public function test_scipro(Scipro $scipro)
    {

        $scipro->auth();
    }
    public function callbackScipro()
    {
        //Store code from auth in cache
        Cache::put('code', $_GET['code'], 60);
        $scipro = new Scipro(Cache::get('code'));
        //Store search in cache for 60 min
        Cache::put('search', 'rydi5898@su.se', 60);
        return $scipro->gettoken();
    }
}
