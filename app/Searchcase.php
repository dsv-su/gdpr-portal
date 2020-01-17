<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Searchcase extends Model
{
    protected $fillable = ['case_id', 'visability', 'gdpr_userid', 'request_pnr','request_email','request_uid', 'status_processed', 'status_flag', 'registrar', 'progress', 'download', 'download_status', 'downloaded'];
    private $request;

    public function initCase($user, $search)
    {
        //Store initial request data to model
        $this->request = Searchcase::create([
            'case_id' => config('services.case.start'),
            'visability' => 1,
            'gdpr_userid' => $user,
            'request_pnr' => $search[0],
            'request_email' => $search[1],
            'request_uid' => $search[2],
            'status_processed' => 0,
            'status_flag' => 1,
            'registrar' => false,
            'progress' => 1,
            'download' => 0,
            'download_status' => 1,
            'downloaded' => 0,
        ]);
        return $this->request;
    }
    public function initnewCase($user, $caseid, $search)
    {
        //Store initial request data to model
        $this->request = Searchcase::create([
            'case_id' => $caseid,
            'visability' => 1,
            'gdpr_userid' => $user,
            'request_pnr' => $search[0],
            'request_email' => $search[1],
            'request_uid' => $search[2],
            'status_processed' => 0,
            'status_flag' => 1,
            'registrar' => false,
            'progress' => 1,
            'download' => 0,
            'download_status' => 1,
            'downloaded' => 0,
        ]);
        return $this->request;
    }

    public function setStatusFlag($value)
    {
        $this->status_flag = $value;
        $this->save();
    }

    public function setDownloadSuccess()
    {
        $this->download = $this->download + 1;
        $this->save();
    }

    public function setDownloadFail()
    {
        $this->download = 0;
        $this->save();
    }

    public function setDownloadStatus($value)
    {
        $this->download_status = $value;
        $this->save();
    }
}
