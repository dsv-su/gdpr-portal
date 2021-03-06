<?php

namespace App\Http\Controllers;

use App\Plugin;
use App\Plugin\Otrs;
use App\Searchcase;
use App\Services\AuthHandler;
use App\Services\ConfigurationHandler;
use App\Status;
use App\Upload;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TestController extends Controller
{

    public function sign()
    {
        if(config('app.debug') == true)
        {
            $case = '2020-1';
            $system = 'MittSystem1';
            $salt = Str::random(32);
            $hash = Hash::make($case.$system.$salt);
            //Remove forward- and backslashes
            $hash = preg_replace('/\\\\/', '', $hash);
            $hash = preg_replace('/\\//', '', $hash);
            $upload = Upload::where('case_id', '=', $case)->first();
            $upload->case_id = $case;
            $upload->system = $system;
            $upload->hash = $hash;
            $upload->save();

            return $link = 'https://gdpr.dev/upload/'. $hash;
        }
        else return redirect()->back();

    }

    public function test_otrs()
    {
        if(config('app.debug') == true)
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
        else return redirect()->back();

    }

    public function plugin_ini()
    {
        if(config('app.debug') == true)
        {
            //$file = Storage::disk('gdpr')->get('gdpr.ini');
            $file = base_path().'/gdpr.ini';
            $config = parse_ini_file($file, true);

            // convert to data to a json string
            $config = json_encode($config);
            // convert back from json, the second parameter is by
            // default false, which will return an object rather than an
            // associative array
            $config = json_decode($config);
            //dd($config);
            dd($config->otrs);
        }
        else return redirect()->back();

    }
    public function ini()
    {
        if(config('app.debug') == true)
        {
            //var_dump($this->getDirContents(base_path().'/pluginconfig/'));
            //var_dump($this->getFiles(base_path().'/pluginconfig/'));
            if(class_exists(AuthHandler::class))
                dd($system = app()->make('SystemService')->authorize());
            /*
            $test = new AuthHandler();
            dd($test->authorize());
            $list = new ConfigurationHandler();
            $list->system();
            */
            //$list->handle_plugins();
        }
        else return redirect()->back();

    }
    private function getDirContents($dir, &$results=array())
    {
        if(config('app.debug') == true)
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
        else return redirect()->back();

    }

    private function getFiles($dir)
    {
        if(config('app.debug') == true)
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
        else return redirect()->back();

    }

}
