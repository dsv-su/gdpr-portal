<?php

namespace App\Services;

use App\Plugin;
use App\Searchcase;
use App\Status;
use App\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Zip;
use File;

class CaseStore extends Model
{
    public function __construct($case)
    {
        $this->case = $case;

    }

    public function errorMessage(Plugin $plugin, $error)
    {
        switch ($error)
        {
            case 'error':
            Storage::disk('public')->put($this->case->case_id . '/raw/'.$plugin->name. '/error_400' . '.txt', 'System Fel - systemet returnerade 400 eller 404');
            break;
            case 'not_found':
            Storage::disk('public')->put($this->case->case_id . '/raw/'.$plugin->name. '/error_204' . '.txt', 'Användaren hittades inte - systemet returnerade 204');
            break;
            case 'mismatch':
            Storage::disk('public')->put($this->case->case_id . '/raw/'.$plugin->name. '/error_409' . '.txt', 'Användardatan överensstämmer inte - det finns dubbletter.');
            break;
        }

    }

    public function makedfolders()
    {
        //Make case directory
        Storage::makeDirectory('/public/' . $this->case->case_id);
        //Make zip directory
        Storage::makeDirectory('/public/'. $this->case->case_id . '/zip/');
        //Make unzipzip directory
        Storage::makeDirectory('/public/'. $this->case->case_id . '/raw/');
    }

    public function makesystemfolder($system)
    {
        //Make system unzipzip directory
        Storage::makeDirectory('/public/'.$this->case->case_id.'/raw/'.$system);
    }

    public function storeZip($system, $file)
    {
        //Store retrived zipfile in directory
        Storage::disk('public')->put($this->case->case_id.'/zip/'.$this->case->case_id.'_'.$system.'.zip', $file);
    }

    public function storePdf($system, $filename, $file)
    {
        //Store retrived pdffile directly in directory
        Storage::disk('public')->put($this->case->case_id.'/raw/'.$system.'/'.$filename.'.pdf', $file);
    }

    public function unzip($system)
    {
        // extract retrived archive to raw directory/folder
        $target_path = base_path() . '/storage/app/public/'.$this->case->case_id.'/zip/'.$this->case->case_id.'_'.$system.'.zip';
        $dest_path = base_path() . '/storage/app/public/'.$this->case->case_id.'/raw/'.$system.'/';
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
        if($case->downloaded < 1) {
            //Creates a zip file of the entire raw folder
            $zip = Zip::create( $zipFileName);
            $zip->add($public_dir, true); //Zip only contents of file
            $zip->add($public_dir . $zipFileName);
            $zip->close();
            $case->downloaded = 1;
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
        //$case->registrar = 0;
        //$case->plugins_processed = 0;
        $case->save();
        //Delete status data
        $deletedRows = Status::where('searchcase_id', $id)->delete();
        //Delete sent system email requests
        if(Upload::where('case_id','=',$case->case_id)){
            $email = Upload::where('case_id', $case->case_id)->delete();
        }
    }

    public function delete_empty_case($id)
    {
        //Get the case
        $case = Searchcase::find($id);
        //Delete directory structure
        Storage::deleteDirectory('/public/'.$case->case_id);
        //Delete status data
        //$deletedRows = Status::where('searchcase_id', $id)->delete();
    }

    //ForceDelete: Only during developing and deploying testing
    public function dev_delete_case($id)
    {
        //Get the case
        $case = Searchcase::find($id);
        //Delete cpmpact zip-file
        //unlink($case->case_id.'.zip');
        //Delete entire directory structure

        Storage::deleteDirectory('/public/'.$case->case_id);
        $case->delete(); //Added temp to test checkboxview uncomment down
        // Reset flags
        //$case->visability = 0;
        //$case->registrar = 0;
        //$case->plugins_processed = 0;
        //$case->save();
        //Delete status data
        $deletedRows = Status::where('searchcase_id', $id)->delete();
        if(Upload::where('case_id','=',$case->case_id)){
            $email = Upload::where('case_id', $case->case_id)->delete();
        }


    }

    //Only during developing and deploying testing
    public function dev_delete()
    {
    //Delete directory structure
    //Storage::deleteDirectory('/public/raw');
    //Storage::deleteDirectory('/public/zip');
    //Storage::deleteDirectory('/public/2020-1');
    Storage::deleteDirectory('/public/2020-6');
    Storage::deleteDirectory('/public/2020-7');
    dd('Done');
    }
}
