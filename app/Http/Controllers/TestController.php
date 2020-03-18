<?php

namespace App\Http\Controllers;

use App\Plugin;
use App\Plugin\Moodle;
use App\Plugin\TestScipro;
use App\Plugin\Otrs;
use App\Searchcase;
use App\Services\CaseStore;
use App\Services\ConfigurationHandler;
use App\Status;
use App\Toker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TestController extends Controller
{
    public function auth()
    {
        $case = Searchcase::latest()->first();
        $plugindriver = new Plugin();
        $plugin = $plugindriver->getPlugin('Scipro');
        $status = Status::where([
            ['searchcase_id','=', $case->id],
            ['plugin_id', '=', $plugin->id],
        ])->first();

        $toker =new Toker($case, $plugin, $status);
        $toker->auth();
    }
    public function callback()
    {
        //Retrive code from callback
        $code = $_GET['code'];
    }

    public function gettoken()
    {
        //Retrive code from callback
        $code = $_GET['code'];
        $case = Searchcase::latest()->first();
        $plugindriver = new Plugin();
        $plugin = $plugindriver->getPlugin('Scipro');
        $status = Status::where([
            ['searchcase_id','=', $case->id],
            ['plugin_id', '=', $plugin->id],
        ])->first();
        dd('Done');
        $token = new Toker($case, $plugin, $status);
        $access_token = $token->getToken($code);
        $status->token = $access_token;
        $status->save();


    }

    //Test connection to scipro with auth-> and callback.
    public function test_scipro()
    {
        $case = Searchcase::latest()->first();
        $plugindriver = new Plugin();
        $plugin = $plugindriver->getPlugin('Scipro');
        $status = Status::where([
            ['searchcase_id','=', $case->id],
            ['plugin_id', '=', $plugin->id],
        ])->first();
        $testscipro = new TestScipro();
        $testscipro->getResource($status->token);
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
        //dd($config);
        // convert to data to a json string
        $config = json_encode($config);
        // convert back from json, the second parameter is by
        // default false, which will return an object rather than an
        // associative array
        $config = json_decode($config);
        //dd($config);
        dd($config->otrs);
    }
    public function ini()
    {
        //var_dump($this->getDirContents(base_path().'/pluginconfig/'));
        //var_dump($this->getFiles(base_path().'/pluginconfig/'));
        $list = new ConfigurationHandler();
        $list->system();
        //$list->handle_plugins();
    }
    private function getDirContents($dir, &$results=array())
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;

    }

    private function getFiles($dir)
    {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

        $files = array();

        foreach ($rii as $file) {

            if ($file->isDir()){
                continue;
            }

            //$files[] = $file->getPathname();
            $files[] = $file->getFilename();

        }
        return $files;
    }

    public function video()
    {
        return view('video.test');
    }
}
