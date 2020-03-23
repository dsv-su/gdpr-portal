<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = ['plugin', 'name', 'client_id', 'client_secret', 'auth', 'auth_url', 'base_uri', 'redirect_uri', 'endpoint_url','owner_email', 'status'];

    public function getPlugin($name)
    {
        return  Plugin::where('name', '=', $name)->first();
    }

}
