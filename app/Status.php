<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['searchcase_id', 'plugin_id', 'plugin_name', 'status', 'download_status'];
}
