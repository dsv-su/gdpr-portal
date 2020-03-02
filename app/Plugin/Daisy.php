<?php

namespace App\Plugin;

use App\Plugin;
use App\Searchcase;
use App\Status;
use GuzzleHttp\Client;

class Daisy
{
    protected $case, $plugin, $status;

    public function __construct(Searchcase $case, Plugin $plugin, Status $status)
    {
        $this->case = $case;
        $this->plugin = $plugin;
        $this->status = $status;
    }

    public function auth()
    {
        $this->status->auth = 1;
        $this->status->save();
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
                $this->status->setZip();
                return $zip;
            } else
                return $response->getStatusCode();

        }
    }
}
