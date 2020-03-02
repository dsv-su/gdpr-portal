<?php

namespace App\Plugin;

use GuzzleHttp\Client;

class Ryan
{
    protected $case, $plugin, $status;

    public function __construct($case, $plugin, $status)
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

    public function getRyan()
    {
        $client = new Client();
        try {
            $response = $client->get($this->plugin->base_uri .'=1&username='. $this->case->request_uid);

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
                $this->status->setZip();
                return $zip;
            } else
                return $response->getStatusCode();

        }
    }
}
