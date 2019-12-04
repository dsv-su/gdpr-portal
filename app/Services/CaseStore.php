<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class CaseStore extends Model
{
    public function makedfolders($system)
    {
        //Make case directory
        Storage::makeDirectory('/public/'.Cache::get('request'));
        //Make zip directory
        Storage::makeDirectory('/public/'.Cache::get('request').'/zip/');
        //Make unzipzip directory
        Storage::makeDirectory('/public/'.Cache::get('request').'/raw/'.$system);
    }

    public function storeZip($system, $file)
    {
        //Store zipfile in directory
        Storage::disk('public')->put(Cache::get('request').'/zip/'.Cache::get('request').'_'.$system.'.zip', $file);
    }
    public function unzip($system)
    {
        // extract whole archive
        //echo Cache::get('request');
        $target_path = base_path() . '/storage/app/public/'.Cache::get('request').'/zip/'.Cache::get('request').'_'.$system.'.zip';
        $dest_path = base_path() . '/storage/app/public/'.Cache::get('request').'/raw/'.$system.'/';
        $zip = new ZipArchive();
        $x = $zip->open($target_path);
        $zip->extractTo($dest_path);
    }
}
