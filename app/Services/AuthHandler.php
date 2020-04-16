<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class AuthHandler extends Model
{
    private $process, $files, $file;
    protected $plugindir, $list, $filename;
    public $config;

    private function getFiles($dir)
    {
        //Get list of filenames
        $this->process = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

        $this->files = array();

        foreach ($this->process as $this->file) {

            if ($this->file->isDir()){
                continue;
            }

            //$files[] = $file->getPathname();
            $this->files[] = $this->file->getFilename();

        }
        return $this->files;
    }

    public function authorize()
    {
        $this->plugindir = base_path().'/systemconfig/';
        $this->list = $this->getFiles($this->plugindir);
        foreach ($this->list as $this->filename) {
            // Read the .ini file and store in table
            if (substr($this->filename, -3) == 'ini') {
                $this->file = $this->plugindir . $this->filename;
                if (!file_exists($this->file)) {
                    $this->file = $this->plugindir . $this->filename . '.example';
                }
                $this->config = parse_ini_file($this->file, true);

                $this->config = json_encode($this->config);
                $this->config = json_decode($this->config);

            }
        }
        return $this->config;

    }
}
