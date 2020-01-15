<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Searchcase extends Model
{
    protected $fillable = ['case_id', 'visability', 'gdpr_userid', 'request_pnr','request_email','request_uid', 'status_processed', 'status_flag', 'registrar', 'progress', 'download', 'download_status', 'downloaded'];

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
