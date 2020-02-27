<?php

namespace App\Plugin;

use GuzzleHttp\Client;

class Daisy
{
    protected $case, $plugin;

    public function __construct($case, $plugin)
    {
        $this->case = $case;
        $this->plugin = $plugin;
    }

    public function getDaisy()
    {
        $client = new Client();
        try {
            $response = $client->get($this->plugin->base_uri . '?personnummer=' . $this->case->request_pnr. '&anvandarnamn='. $this->case->request_uid. '&epost=' . $this->case->request_email. '&authcode='. $this->plugin->client_secret );
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
