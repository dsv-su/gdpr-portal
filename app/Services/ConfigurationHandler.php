<?php

namespace App\Services;

use App\Plugin;
use App\System;
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

    public function handle_plugins()
    {
        $plugindir = base_path().'/pluginconfig/';
        $list = $this->getFiles($plugindir);
        //dd($list);
        foreach ($list as $filename) {
            // Read the .ini file and store in table
            if (substr($filename, -3) == 'ini') {

                $file = $plugindir . $filename;
                if (!file_exists($file)) {
                    $file = $plugindir . $filename . '.example';
                }
                $config = parse_ini_file($file, true);

                foreach ($config as $configkey=>$configvalue) {
                    $pluginrow = array([$configkey => $configvalue]);

                    foreach ($pluginrow as $config) {

                        $config = json_encode($config);

                        $config = json_decode($config);

                        //Store in Plugin
                        $plugin = new Plugin();
                        $plugintable = $plugin->getFillable();
                        foreach ($config as $key => $item) {
                            $plugin->name = $key;
                            foreach ($plugintable as $pluginitem) {
                                foreach ($item as $key2 => $item2) {
                                    if ($pluginitem == $key2) {
                                        $plugin->$pluginitem = $item2;
                                    }
                                }
                            }
                            $plugin->save();
                        }
                    }
                }

            }
        }
    }

    public function handle_system()
    {
        // Read gdpr.ini file and store id db
        $file = base_path().'/systemconfig/gdpr.ini';
        if (!file_exists($file)) {
            $file = base_path().'/systemconfig/gdpr.ini.example';
        }
        $system_config = parse_ini_file($file, true);
        $conf = new System();
        $conf->newSystem($system_config['case_start_id'], $system_config['case_ttl'], $system_config['registrator'], $system_config['db'], $system_config['db_host'], $system_config['db_port'], $system_config['db_database'], $system_config['db_username'], $system_config['db_password']);
    }
}
