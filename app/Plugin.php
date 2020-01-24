<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = ['name', 'status'];

    public function newPlugin($name)
    {
        Plugin::create([
            'name' => $name,
            'status' => 0,
        ]);
    }

}
