<?php

namespace App\Plugin;
use GuzzleHttp\Client;

class Otrs
{
    private $endpoint_url;

    public function __construct()
    {
        $this->endpoint_url  = 'http://otrs-stage.dsv.su.se/otrs/index.pl';
    }

    public function getOtrs($text)
    {
        $client = new Client();

        try {
            $response = $client->post($this->endpoint_url, [
                'form_params' => [
                    'Action' => 'Login',
                    'RequestedURL' => 'Action=AgentGdprHandler&Subaction=Search&Fulltext=test&OutputFormat=JSON',
                    'User' => 'devtest',
                    'Password' => 'hunter2'
                ]
            ]);

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
            //$response = json_decode($response->getBody()->getContents(), true);
            $body = $response->getBody();
            // Implicitly cast the body to a string and echo it
            echo $body;
            // Explicitly cast the body to a string
            //$stringBody = (string) $body;
            // Read 10 bytes from the body
            //$tenBytes = $body->read(10);
            // Read the remaining contents of the body as a string
            //$remainingBytes = $body->getContents();

  /*
            if ($response->getStatusCode() == 200) {
                dd($body = $response->getBody());

                // Read contents of the body
                $zip = $body->getContents();

                //dd($response->getStatusCode());
                return $zip;
            } else
                return $response->getStatusCode();
*/
        }
    }
}
