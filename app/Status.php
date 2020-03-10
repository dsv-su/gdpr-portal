<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['searchcase_id', 'plugin_id', 'plugin_name', 'status', 'progress_status', 'download_status', 'auth', 'auth_system', 'callback','que'];

    public function initPluginStatus($caseid)
    {
        // FREJA
        $plugins = Plugin::all();
        foreach ( $plugins as $plugin)
        {
            Status::create([
                'searchcase_id' => $caseid,
                'plugin_id' => $plugin->id,
                'plugin_name' => $plugin->name,
                'status' => 300,
                'progress_status' => 100,
                'download_status' => 0,
                'auth' => 0,
                'auth_system' => $plugin->auth,
                'callback' => 0,
                'zip' => 0,
            ]);

        }
    }

    public function setStatus($value)
    {
        switch($value)
        {
            case 'ok':
                $this->status = 200;
                break;
            case 'not_found':
                $this->status = 204;
                break;
            case 'error':
                $this->status = 400;
                break;
            case 'mismatch':
                $this->status = 409;
                break;
            case 'pending':
                $this->status = 300;
        }
        $this->save();
    }

    public function setProgressStatus($value)
    {
        $this->progress_status = $value;
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
    public function setZip()
    {
        $this->zip = 1;
        $this->save();
    }
}
