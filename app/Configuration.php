<?php

namespace App;

use App\Plugin;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    public function initPlugins()
    {
        // Read gdpr.ini file and store id db
        $file = base_path().'/gdpr.ini';
        if (!file_exists($file)) {
            $file = base_path().'/gdpr.ini.example';
        }
        $plugin_config = parse_ini_file($file, true);
        $plugin = new Plugin();
        foreach ($plugin_config as $config)
        {
            $plugin->newPlugin($config['client_name']);
        }
    }
}
