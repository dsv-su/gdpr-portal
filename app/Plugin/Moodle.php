<?php

namespace App\Plugin;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Moodle
{

    public function getMoodle()
    {
        $client = new Client();
        try {
            $response = $client->get('https://ilearn2test.dsv.su.se/gdpr/do_fetch.php?op=1&username=tdsv');
            //$response = $client->get('https://ilearn2test.dsv.su.se/gdpr/do_fetch.php?op=1&username=rydi5898');
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();

        }
        if ($response->getStatusCode() == 200) {
            $body = $response->getBody();

            // Read contents of the body
            $zip = $body->getContents();
            Storage::disk('public')->put(Cache::get('request').'_moodle.zip', $zip);
            //dd($response->getStatusCode());
            return 200;
        } else
            return 401;
    }
}
