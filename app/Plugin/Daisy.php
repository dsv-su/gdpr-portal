<?php

namespace App\Plugin;

use GuzzleHttp\Client;

class Daisy
{
    private $endpoint_url, $authcode;

    public function __construct()
    {
        $this->endpoint_url = config('services.daisy.endpoint_uri');
        $this->authcode = config('services.daisy.authcode');
    }

    public function getDaisy($pnr, $email, $uid)
    {
        $client = new Client();
        try {
            $response = $client->get($this->endpoint_url . '?personnummer=' . $pnr. '&anvandarnamn='.$uid. '&epost=' . $email.'&authcode='.$this->authcode );
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
