<?php

namespace App\Plugin;

use GuzzleHttp\Client;

class Moodle
{
    private $endpoint_url;

    public function __construct()
    {
        $this->endpoint_url  = config('services.moodle-test.endpoint_uri');
    }

    public function getMoodle($req)
    {
        $client = new Client();
        try {
            $response = $client->get($this->endpoint_url . $req);

        } catch (\Exception $e) {
            /**
             * If there is an exception; Client error;
             */
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                return $response->getStatusCode();

            }
        }

        //Processing response from Moodle
        if ($response) {
            if ($response->getStatusCode() == 200) {
                $body = $response->getBody();

                // Read contents of the body
                $zip = $body->getContents();

                //dd($response->getStatusCode());
                return $zip;
            } else
                return $response->getStatusCode();

        }
    }
}
