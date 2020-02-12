<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = ['name', 'client_id', 'client_secret', 'auth_url', 'base_uri', 'redirect_uri', 'endpoint_url', 'status'];

    public function newPlugin($name, $client_id = null, $client_secret = null, $auth_url = null, $base_uri = null, $redirect_uri = null, $endpoint_url = null)
    {
        Plugin::create([
            'name' => $name,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'auth_url' => $auth_url,
            'base_uri' => $base_uri,
            'redirect_uri' => $redirect_uri,
            'endpoint_url' => $endpoint_url,
            'status' => 0,
        ]);
    }

    public function getPlugin($name)
    {
        return  Plugin::where('name', '=', $name)->first();
    }

}
