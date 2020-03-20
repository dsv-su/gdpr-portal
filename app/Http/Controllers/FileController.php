<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessFinished;
use App\Plugin;
use App\Searchcase;
use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index(Request $request, $id, $system)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }
        else
        {
            $data['case_id'] = $id;
            $data['system'] = $system;

            return view('file.home', $data);
        }


    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);


        if($request->hasFile('file'))
        {
            //Store uploaded file in case and system folder
            Storage::putFile('public/'.$request->input('case').'/raw/'.$request->input('system'),
                $request->file('file'));
            //Get plugin
            $plugin = new Plugin();
            $plugin = $plugin->getPlugin($request->input('system'));
            $case = new Searchcase();
            $case = $case->getCase($request->input('case'));

            //Get status of case
            $status = Status::where([
                ['searchcase_id','=', $case->id],
                ['plugin_id', '=', $plugin->id],
            ])->first();
            $status->setStatus('ok'); //$response
            $status->setProgressStatus(100);
            //Send notificcation mail
            $request_finished = new ProcessFinished($case);
            dispatch($request_finished);

            return 'Tack, nu är utdraget uppladdat!';
        }
        else
        {
            return 'Ingen fil vald';
        }


        //return response()->json(['success'=>'Filen har laddats upp!']);
    }
}
