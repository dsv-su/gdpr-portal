<?php

namespace App\Http\Controllers;

use App\Searchcase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use File;
use ZanySoft\Zip\Zip;
use ZipArchive;
use ZanySoft\Zip\ZipFacade;

class DiskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unzip()
    {
        // extract whole archive
        //echo Cache::get('request');
        $target_path = base_path() . '/storage/app/public/2019-1/zip/2019-1_scipro-dev.zip';
        $dest_path = base_path() . '/storage/app/public/2019-1/raw/';
        $zip = new ZipArchive();
        $x = $zip->open($target_path);
        $zip->extractTo($dest_path);
    }

    public function download($id)
    {
        $public_dir = public_path().'/storage/2019-1/raw/';
        $case = Searchcase::find($id);
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

    public function index()
    {
        $public_dir = public_path().'/storage/2019-1/raw/';
        $zip = new ZipArchive;

        $zipFileName = Cache::get('request').'.zip';

        if ($zip->open($public_dir .  $zipFileName, ZipArchive::CREATE) === TRUE)
        {
            $files = File::files($public_dir);

            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }

            $zip->close();
        }
        // Set Header
        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );
        $download = $public_dir.'/download/';
        $filetopath = $download.$zipFileName;
        // Create Download Response
        if(file_exists($filetopath)){
            return response()->download($filetopath,$zipFileName,$headers);
        }
       dd('Done');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
