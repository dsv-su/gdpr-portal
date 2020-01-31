<?php

namespace App\Plugin;

use GuzzleHttp\Client;

class Utbytes
{
    private $endpoint_url;

    public function __construct()
    {
        $this->endpoint_url = config('services.utbytes.endpoint_uri');
    }

    public function getUtbytes($pnr, $email, $uid)
    {
        $client = new Client();
        try {
            $response = $client->get($this->endpoint_url . 'email=' . $email . '&pn=' . $pnr);
        } catch (\Exception $e) {
            /**
             * If there is an exception; Client error;
             */
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                return $response->getStatusCode();

            }
        }

        //Processing response from Utbytes
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