<?php

namespace App\Http\Controllers;

use App\Plugin\Moodle;
use App\Plugin\TestScipro;
use App\Plugin\Otrs;
use App\Services\CaseStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    public function test_scipro(TestScipro $testscipro)
    {
        $testscipro = new TestScipro();
        $testscipro->auth();
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
        $status = $test->getOtrs('test', 'http://otrs-stage.dsv.su.se/otrs/index.pl', 'otrs');
        //Create folders for retrived data
        $dir = new CaseStore();
        $dir->makesystemfolder('otrs');

        //Store zipfile in directory
        $dir->storePdf('otrs', $status);
    }

    public function plugin_ini()
    {
        //$file = Storage::disk('gdpr')->get('gdpr.ini');
        $file = base_path().'/gdpr.ini';
        $config = parse_ini_file($file, true);
        //dd($config['scipro_dev']['client_name']);
        dd($config);
        // convert to data to a json string
        $config = json_encode($config);
        // convert back from json, the second parameter is by
        // default false, which will return an object rather than an
        // associative array
        $config = json_decode($config);

    }
    public function video()
    {
        return view('video.test');
    }
}
