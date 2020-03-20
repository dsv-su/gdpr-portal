<?php

namespace App\Services;

use App\Plugin;
use App\System;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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

    public function check_system()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('systems')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->system();
    }

    public function system()
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
                $system = new System();
                foreach ($config as $configkey => $configvalue) {
                    //$pluginrow = array([$configkey => $configvalue]);
                    //dd($configvalue);
                        $config = json_encode($configvalue);
                        $config = json_decode($config);
                        //dd($config);
                        //Store in Plugin
                        $systemtable = $system->getFillable();
                        foreach ($config as $key => $item) {
                            //dd($key);
                            //$plugin->name = $key;
                            foreach ($systemtable as $systemitem) {
                                    if ($systemitem == $key) {
                                        $system->$systemitem = $item;
                                        }
                                    }
                            }

                        }
                $system->save();
                }

            }

    }
    public function check_plugins()
    {
        $plugindir = base_path().'/pluginconfig/';
        $list = $this->getFiles($plugindir);
        //dd($list);
        foreach ($list as $filename) {
            // Read the .ini file and store in table
            if (substr($filename, -3) == 'ini') {
                $plugin_files[] =  $filename;
            }

        }
        $loaded_plugins = Plugin::all()->pluck('name');
        if($loaded_plugins->count() != count($plugin_files))
        {
            //Insert the new pluginconf
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('plugins')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->handle_plugins();
        }
        else
        {
            //Update the existing pluginconf
            $this->update_plugins();
        }
        //dd($loaded_plugins);
        //dd($plugin_files);
    }
    public function update_plugins()
    {
        $plugindir = base_path().'/pluginconfig/';
        $list = $this->getFiles($plugindir);
        $x = 1;
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

                        //Update in Plugin
                        //$plugin = new Plugin();
                        $plugin_instance = new Plugin();

                        $plugintable = $plugin_instance->getFillable();
                        foreach ($config as $key => $item) {
                            $plugin = Plugin::find($x);
                            $plugin->name = $key;
                            foreach ($plugintable as $pluginitem) {
                                foreach ($item as $key2 => $item2) {
                                    if ($pluginitem == $key2) {
                                        $plugin->$pluginitem = $item2;
                                    }

                                }

                            }
                            $plugin->save();
                            $x++;
                        }

                    }

                }

            }
        }
    }
}
