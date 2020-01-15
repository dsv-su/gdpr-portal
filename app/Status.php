<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['searchcase_id', 'plugin_id', 'plugin_name', 'status', 'download_status'];

    public function setStatus($value)
    {
        $this->status = $value;
        $this->save();
    }

    public function setDownloadStatus($value)
    {
        $this->download_status = $value;
        $this->save();
    }

    public function getStatus()
    {
        return $this->status;
    }
}
