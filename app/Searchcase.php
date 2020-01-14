<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Searchcase extends Model
{
    protected $fillable = ['case_id', 'visability', 'gdpr_userid', 'request_pnr','request_email','request_uid', 'status_processed', 'status_flag', 'registrar', 'progress', 'download', 'download_status', 'downloaded'];
}
