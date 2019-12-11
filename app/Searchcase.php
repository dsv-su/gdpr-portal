<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Searchcase extends Model
{
    protected $fillable = ['case_id', 'visability', 'request_pnr','request_email','request_uid', 'status_moodle_test','status_scipro_dev','registrar','download_moodle_test', 'download_scipro_dev','download'];
}
