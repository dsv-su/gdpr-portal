<?php

namespace App\Plugin;

use GuzzleHttp\Client;

class Moodle
{

    public function getMoodle($pnr, $email, $uid, $endpoint_url)
    {
        $client = new Client();
        try {
            $response = $client->get($endpoint_url .'=1&username='. $uid);

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
