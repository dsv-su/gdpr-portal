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
use App\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Illuminate\Support\Facades\URL;

class TestController extends Controller
{
    public function sign()
    {
        $hash = Hash::make('2020-1'.'MittSystem1');
        //Remove forward- and backslashes
        $hash = preg_replace('/\\\\/', '', $hash);
        $hash = preg_replace('/\\//', '', $hash);
        $upload = new Upload();
        $upload->case_id = '2020-1';
        $upload->system = 'MittSystem1';
        $upload->hash = $hash;
        $upload->save();
        $upload = new Upload();

        return $link = 'https://gdpr.dev/upload/'. $hash;
    }
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
        $case = Searchcase::latest()->first();
        $plugin = Plugin::where('name','=','Otrs')->first();
        $status = Status::where([
            ['searchcase_id','=', $case->id],
            ['plugin_id', '=', $plugin->id],
        ])->first();

        $test = new Otrs($case, $plugin,$status);
        $status = $test->getResource();
        dd('Done');

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
