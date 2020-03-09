<?php

namespace App\Plugin;

use GuzzleHttp\Client;

class Utbytes extends GenericPlugin
{

    public function getUtbytes()
    {
        $client = new Client();
        try {
            $response = $client->get($this->plugin->base_uri . 'email=' . $this->case->request_email . '&pn=' . $this->case->request_pnr);
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
                $this->status->setZip();
                return $zip;
            } else
            {
                $this->status->setDownloadStatus(0);

                return $response->getStatusCode();
            }


        }
    }
}
