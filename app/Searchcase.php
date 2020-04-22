<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Searchcase extends Model
{
    protected $fillable = ['case_id', 'visability', 'gdpr_userid','gdpr_useremail','gdpr_server', 'request_pnr','request_email','request_uid', 'status_processed', 'status_flag', 'registrar', 'progress', 'plugins_processed', 'download_status', 'downloaded'];
    private $request;

    public function initCase($user, $search, $case_id)
    {
        if(app()->environment('local'))
        {
            $requester_email = 'ryan@dsv.su.se';
        }
        else {
            $requester_email = $_SERVER['mail'];
        }
        $server = $_SERVER['HTTP_HOST'];
        //Store initial request data to model
        $this->request = Searchcase::create([
            'case_id' => $case_id,
            'visability' => 1,
            'gdpr_userid' => $user,
            'gdpr_useremail' => $requester_email,
            'gdpr_server' => $server,
            'request_pnr' => $search[0],
            'request_email' => $search[1],
            'request_uid' => $search[2],
            'status_processed' => 0,
            'status_flag' => 1,
            'registrar' => false,
            'progress' => 1,
            'plugins_processed' => 0,
            'download_status' => 1,
            'downloaded' => 0,
        ]);
        return $this->request;
    }

    public function initnewCase($user, $caseid, $search)
    {
        if(app()->environment('local'))
        {
            $requester_email = 'ryan@dsv.su.se';
        }
        else {
            $requester_email = $_SERVER['mail'];
        }
        $server = $_SERVER['HTTP_HOST'];
        //Store initial request data to model
        $this->request = Searchcase::create([
            'case_id' => $caseid,
            'visability' => 1,
            'gdpr_userid' => $user,
            'gdpr_useremail' => $requester_email,
            'gdpr_server' => $server,
            'request_pnr' => $search[0],
            'request_email' => $search[1],
            'request_uid' => $search[2],
            'status_processed' => 0,
            'status_flag' => 1,
            'registrar' => false,
            'progress' => 1,
            'plugins_processed' => 0,
            'download_status' => 1,
            'downloaded' => 0,
        ]);
        return $this->request;
    }

    public function getCase($name)
    {
        return  Searchcase::where('case_id', '=', $name)->first();
    }

    public function setRegistrar()
    {
        $this->registrar = true;
        $this->sent_registrar = now();
        $this->save();
    }

    public function setStatusProcessed()
    {
        //Increase for each processed plugin
        $this->status_processed++;
        $this->save();
    }

    public function setStatusFlag($value)
    {
        $this->status_flag = $value;
        $this->save();
    }

    public function setProgress($value)
    {
        $this->progress = $value;
        $this->save();
    }

    public function setPluginSuccess()
    {
        $this->plugins_processed++;
        $this->save();
    }

    public function setPluginPending()
    {
        $this->plugins_processed--;
        $this->save();
    }

    public function setPluginFail()
    {
        $this->plugins_processed = 0;
        $this->save();
    }

    public function setDownloadStatus($value)
    {
        $this->download_status = $value;
        $this->save();
    }
}
