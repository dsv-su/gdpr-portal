<?php

namespace App\Services;

use App\Searchcase;
use App\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Zip;
use File;

class CaseStore extends Model
{
    public function makedfolders()
    {
        //Make case directory
        Storage::makeDirectory('/public/'.Cache::get('request'));
        //Make zip directory
        Storage::makeDirectory('/public/'.Cache::get('request').'/zip/');
        //Make unzipzip directory
        Storage::makeDirectory('/public/'.Cache::get('request').'/raw/');
    }

    public function makesystemfolder($system)
    {
        //Make system unzipzip directory
        Storage::makeDirectory('/public/'.Cache::get('request').'/raw/'.$system);
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
        //Check if zip has been created
        if($x = $zip->open($target_path))
        {
            //If yes; extract to destination
            $zip->extractTo($dest_path);
        }
    }

    public function makezip($id)
    {
        //Create zip file of retrieved files and folders
        $case = Searchcase::find($id);
        $destination = public_path().'/storage/'.$case->case_id.'/';
        $zipFileName = $destination.$case->case_id.'.zip';
        //Directory of unzipped files
        $public_dir = public_path().'/storage/'.$case->case_id.'/raw/';
        //Check if zip already has been created
        if($case->download<3) {
            //Creates a zip file of the entire raw folder
            $zip = Zip::create( $zipFileName);
            $zip->add($public_dir, true); //Zip only contents of file
            $zip->add($public_dir . $zipFileName);
            $zip->close();
            $case->download=3;
            $case->save();
        }

    }

    public function download($id)
    {
        //Download zip file
        $case = Searchcase::find($id);
        //Set public folder
        $public_dir = public_path().'/storage/'.$case->case_id.'/';
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
        $public_dir = public_path().'/storage/'.$case->case_id.'/';
        unlink($public_dir.$case->case_id.'.zip');
        //Delete directory structure
        Storage::deleteDirectory('/public/'.$case->case_id);
        //Reset flags
        $case->visability = 0;
        $case->status_scipro_dev = 0;
        $case->status_moodle_test = 0;
        $case->registrar = 0;
        $case->download = 0;
        $case->save();
        //Delete status data
        $deletedRows = Status::where('searchcase_id', $id)->delete();

    }

    //ForceDelete: Only during developing and deploying testing
    public function dev_delete_case($id)
    {
        //Get the case
        $case = Searchcase::find($id);
        //Delete cpmpact zip-file
        //unlink($case->case_id.'.zip');
        //Delete directory structure
        Storage::deleteDirectory('/public/'.$case->case_id);
        // Reset flags
        $case->visability = 0;
        $case->status_scipro_dev = 0;
        $case->status_moodle_test = 0;
        $case->registrar = 0;
        $case->download = 0;
        $case->save();
        //Delete status data
        $deletedRows = Status::where('searchcase_id', $id)->delete();
    }

    //Only during developing and deploying testing
    public function dev_delete()
    {
    //Delete directory structure
    Storage::deleteDirectory('/public/raw');
    Storage::deleteDirectory('/public/zip');
    }
}
