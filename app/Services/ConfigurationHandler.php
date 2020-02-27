<?php

namespace App\Services;

use App\Plugin;
use Illuminate\Database\Eloquent\Model;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ConfigurationHandler extends Model
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

    public function handle()
    {
        $plugindir = base_path().'/pluginconfig/';
        $list = $this->getFiles($plugindir);

        foreach ($list as $filename)
        {
            // Read the plugin .ini file and store in table
            $file = $plugindir . $filename;
            if (!file_exists($file)) {
                $file = $plugindir . $filename.'.example';
            }
            $config = parse_ini_file($file, true);
            $config = json_encode($config);

            $config = json_decode($config);
            //Store in Plugin
            $plugin = new Plugin();
            $plugintable = $plugin->getFillable();
            foreach($config as $key=>$item) {
                $plugin->name = $key;
                foreach($plugintable as $pluginitem) {
                    foreach ($item as $key2 => $item2) {
                        if ($pluginitem == $key2)
                        {
                            $plugin->$pluginitem = $item2;
                        }
                        }
                }
                $plugin->save();
            }
        }

    }
}
