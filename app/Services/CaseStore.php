<?php

namespace App\Services;

use App\Searchcase;
use Cassandra\Exception\TruncateException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Zip;
use File;

class CaseStore extends Model
{
    //public function makedfolders($system)
    public function makedfolders()
    {
        //Make case directory
        Storage::makeDirectory('/public/'.Cache::get('request'), $mode=0775);
        //Make zip directory
        Storage::makeDirectory('/public/'.Cache::get('request').'/zip/');
        //Make unzipzip directory
        Storage::makeDirectory('/public/'.Cache::get('request').'/raw/');
        //Storage::makeDirectory('/public/'.Cache::get('request').'/raw/'.$system, $mode=0775);
    }

    public function makesystemfolder($system)
    {
        //Make system unzipzip directory
        Storage::makeDirectory('/public/'.Cache::get('request').'/raw/'.$system, $mode=0775);
    }

    public function storeZip($system, $file)
    {
        //Store retried zipfile in directory
        Storage::disk('public')->put(Cache::get('request').'/zip/'.Cache::get('request').'_'.$system.'.zip', $file);
    }
    public function unzip($system)
    {
        // extract retrived archive to raw directory/folder
        $target_path = base_path() . '/storage/app/public/'.Cache::get('request').'/zip/'.Cache::get('request').'_'.$system.'.zip';
        $dest_path = base_path() . '/storage/app/public/'.Cache::get('request').'/raw/'.$system.'/';
        $zip = new ZipArchive();
        $x = $zip->open($target_path);
        $zip->extractTo($dest_path);
    }

    public function makezip($id)
    {
        //Create zip file of retrieved files and folders
        $case = Searchcase::find($id);
        $zipFileName = $case->case_id.'.zip';
        $public_dir = public_path().'/storage/'.$case->case_id.'/raw/';
        //Check if zip already has been created
        if($case->download<3) {
            //Creates a zip file of the entire raw folder
            $zip = Zip::create($zipFileName);
            $zip->add($public_dir);
            $zip->add($public_dir . $zipFileName);
            $zip->close();
            $case->download++;
            $case->save();
        }



    }

    public function download($id)
    {
        //Download zip file
        $case = Searchcase::find($id);
        //Set public folder
        $public_dir = public_path().'/';
        //Set filename
        $zipFileName = $case->case_id.'.zip';
        // Set Header
        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );

        $filetopath = $public_dir.$zipFileName;
        // Create Download Response
        if(file_exists($filetopath)){
            return response()->download($filetopath,$zipFileName,$headers);
        }

    }

    public function delete_case($id)
    {
        //Get the case
        $case = Searchcase::find($id);
        //Delete cpmpact zip-file
        unlink($case->case_id.'.zip');
        //Delete directory structure
        Storage::deleteDirectory('/public/'.$case->case_id);
        //
        $case->visability = 0;
        //$case->request_pnr = '';
        //$case->request_email = '';
        //$case->request_uid = '';
        $case->status_scipro_dev = 0;
        $case->status_moodle_test = 0;
        $case->registrar = 0;
        $case->download = 0;
        $case->save();

    }
}
