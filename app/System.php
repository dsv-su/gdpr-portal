<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    protected $fillable = ['case_start_id', 'case_ttl', 'registrator', 'db', 'db_host', 'db_port', 'db_database', 'db_username', 'db_password'];

    public function newSystem($case_start_id, $case_ttl, $registrator, $db, $db_host, $db_port, $db_database, $db_username, $db_password)
    {
        System::create([
            'case_start_id' => $case_start_id,
            'case_ttl' => $case_ttl,
            'registrator' => $registrator,
            'db' => $db,
            'db_host' => $db_host,
            'db_port' => $db_port,
            'db_database' => $db_database,
            'db_username' => $db_username,
            'db_password' => $db_password,
        ]);
    }
}
