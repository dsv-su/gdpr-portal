<?php

namespace App\Http\Controllers;

use App\Plugin\Moodle;
use App\Plugin\Scipro;
use App\Plugin\Otrs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
    public function test_moodle()
    {
        $moodle = new Moodle();

        return $status = $moodle->getMoodle('tdsv');
    }

    public function test_otrs()
    {
        $test = new Otrs();
        $test->getOtrs('test');
    }

    public function plugin_ini()
    {
        //$file = Storage::disk('gdpr')->get('gdpr.ini');
        $file = base_path().'/gdpr.ini';
        $config = parse_ini_file($file, true);
        dd($config['scipro_dev']['client_name']);
        // convert to data to a json string
        $config = json_encode($config);
        // convert back from json, the second parameter is by
        // default false, which will return an object rather than an
        // associative array
        $config = json_decode($config);

    }
}
