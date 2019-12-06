<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Searchcase extends Model
{
    protected $fillable = ['case_id','request', 'status_moodle_test','status_scipro_dev','registrar','download'];
}
