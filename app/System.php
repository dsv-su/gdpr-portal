<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    protected $fillable = ['case_start_id', 'case_ttl', 'plugin_tries', 'plugin_request_timeout', 'registrator', 'db', 'db_host', 'db_port', 'db_database', 'db_username', 'db_password', 'client_id',
        'client_secret', 'auth_url', 'base_uri', 'redirect_uri'];

    public function newSystem($case_start_id, $case_ttl, $plugin_tries, $plugin_request_timeout, $registrator, $db, $db_host, $db_port, $db_database, $db_username, $db_password)
    {
        System::create([
            'case_start_id' => $case_start_id,
            'case_ttl' => $case_ttl,
            'plugin_tries' => $plugin_tries,
            'plugin_request_timeout' => $plugin_request_timeout,
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
