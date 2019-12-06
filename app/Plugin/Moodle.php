<?php

namespace App\Plugin;

use GuzzleHttp\Client;

class Moodle
{
    private $endpoint_url;

    public function __construct()
    {
        $this->endpoint_url  = "https://ilearn2test.dsv.su.se/gdpr/moodle.php?op=1&username=";
    }

    public function getMoodle($req)
    {
        $client = new Client();
        try {
            $response = $client->get($this->endpoint_url.$req);

        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();

        }
        if ($response->getStatusCode() == 200) {
            $body = $response->getBody();

            // Read contents of the body
            $zip = $body->getContents();

            //dd($response->getStatusCode());
            return $zip;
        } else
            return 401;
    }
}
