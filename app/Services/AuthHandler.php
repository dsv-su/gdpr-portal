<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class AuthHandler extends Model
{
    private function getFiles($dir)
    {
        //Get list of filenames
        $process = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

        $files = array();

        foreach ($process as $file) {

            if ($file->isDir()){
                continue;
            }

            //$files[] = $file->getPathname();
            $files[] = $file->getFilename();

        }
        return $files;
    }

    public function authorize()
    {
        $plugindir = base_path().'/systemconfig/';
        $list = $this->getFiles($plugindir);
        foreach ($list as $filename) {
            // Read the .ini file and store in table
            if (substr($filename, -3) == 'ini') {
                $file = $plugindir . $filename;
                if (!file_exists($file)) {
                    $file = $plugindir . $filename . '.example';
                }
                $config = parse_ini_file($file, true);

                $config = json_encode($config);
                $config = json_decode($config);

            }
        }
        return $config;

    }
}
