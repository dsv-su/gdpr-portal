<?php

namespace App\Plugin;

use GuzzleHttp\Client;

class Utbytes extends GenericPlugin
{
    private $response;

    public function getResource()
    {
        $client = new Client(['base_uri' => $this->plugin->base_uri]);
        $headers = [
            'Authorization' =>  $this->status->token,
            'Accept' => 'application/json',
        ];
        try {
            $this->response = $client->request('GET', $this->plugin->base_uri . 'email=' . $this->case->request_email . '&pn=' . $this->case->request_pnr, [
                'headers' => $headers
            ]);
        } catch (\Exception $e) {
            /**
             * If there is an exception; Client error;
             */
            if ($e->hasResponse()) {
                $this->response = $e->getResponse();
                switch($this->response->getStatusCode())
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
                //return $this->response->getStatusCode();
                return 'error';

            }
        }

        //Processing response from Utbytes
        if ($this->response) {
            if ($this->response->getStatusCode() == 200) {
                $body = $this->response->getBody();

                // Read contents of the body
                $zip = $body->getContents();

                //dd($response->getStatusCode());
                $this->status->setZip();
                return $zip;
            } else
            {
                $this->status->setDownloadStatus(0);
                switch($this->response->getStatusCode())
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
