<?php

namespace App\Plugin;

use GuzzleHttp\Client;

class Daisy
{

    public function getDaisy($pnr, $email, $uid, $endpoint_url, $authcode)
    {
        $client = new Client();
        try {
            $response = $client->get($endpoint_url . '?personnummer=' . $pnr. '&anvandarnamn='.$uid. '&epost=' . $email. '&authcode='. $authcode );
        } catch (\Exception $e) {
            /**
             * If there is an exception; Client error;
             */
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                return $response->getStatusCode();

            }
        }

        //Processing response from Daisy
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
