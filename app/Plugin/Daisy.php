<?php

namespace App\Plugin;

use GuzzleHttp\Client;

class Daisy extends GenericPlugin
{

    public function getResource() //getDaisy()
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
                switch($response->getStatusCode())
                {
                    case 204:
                        return 'not_found';
                        break;
                    case 400:
                        return 'error';
                        break;
                    case 401:
                        return 'error';
                        break;
                    case 404:
                        return 'error';
                        break;
                    case 409:
                        return 'mismatch';
                        break;
                }
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
            {
                $this->status->setDownloadStatus(0);
                switch($response->getStatusCode())
                {
                    case 204:
                        return 'not_found';
                        break;
                    case 400 or 401 or 404:
                        return 'error';
                        break;
                    case 409:
                        return 'mismatch';
                        break;
                }

                return 'error';
            }


        }
    }
}
