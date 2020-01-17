<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['searchcase_id', 'plugin_id', 'plugin_name', 'status', 'download_status'];

    public function initPluginStatus($caseid)
    {
        $plugins = Plugin::all();
        foreach ( $plugins as $plugin)
        {
            Status::create([
                'searchcase_id' => $caseid,
                'plugin_id' => $plugin->id,
                'plugin_name' => $plugin->name,
                'status' => 200,
                'download_status' => 0,
            ]);

        }
    }

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
