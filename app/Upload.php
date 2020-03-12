<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = ['filename'];

    public function case()
    {
        return $this->belongsTo(Searchcase::class);
    }
}
