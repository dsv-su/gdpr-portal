<?php

namespace App\Plugin;
use GuzzleHttp\Client;
use App\Services\GuzzleJson;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Handler\CurlHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Otrs
{
    private $endpoint_url;

    public function __construct()
    {
        $this->endpoint_url  = 'http://otrs-stage.dsv.su.se/otrs/index.pl';
    }

    public function getOtrs($text)
    {
        $client = new Client(['cookies' => true]);

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

        //Processing response
        if ($response) {
            //$response = json_decode($response->getBody()->getContents(), true);
            $body = $response->getBody();
            /*
            echo $body;
            echo '<br><hr>';
            */
            //Removing trash from string to return a valid json
            $body = substr($body, 0, -10);
            //echo '<br><hr>';
            $body = $body.']}';
            //echo '<br><hr>';
            $json = json_decode($body);
            /*
            echo '<br><hr>';
            echo json_last_error();
            echo '<br><hr>';

            */
            //($json->tickets);
            $clientpdf = new Client();

            foreach ($json->tickets as $value)
            {
                //dd($value->TicketID);
                try {
                    $response = $clientpdf->post($this->endpoint_url, [
                        'form_params' => [
                        'Action' => 'Login',
                        'RequestedURL' => 'Action=GdprTicketPrint;TicketID='. $value->TicketID,
                        'Lang' => 'sv',
                        'User' => 'devtest',
                        'Password' => 'hunter2',
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
                    if ($response->getStatusCode() == 200) {

                        //dd($response);
                        $body = $response->getBody();

                        // Read contents of the body
                        $pdf = $body->getContents();

                        //dd($response->getStatusCode());
                        Storage::disk('public')->put(Cache::get('request').'/zip/'.Cache::get('request').'_'.$value->TicketID.'.pdf', $pdf);
                        //return $pdf;
                    } else
                        return $response->getStatusCode();

                }
            }
        }
        dd('Done');
    }

    public function getPdf()
    {
        $clientpdf = new Client();
        try {
            $response = $clientpdf->post($this->endpoint_url, [
                'form_params' => [
                    'Action' => 'Login',
                    'RequestedURL' => 'Action=GdprTicketPrint;TicketID=240950',
                    'Lang' => 'sv',
                    'User' => 'devtest',
                    'Password' => 'hunter2',
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
            if ($response->getStatusCode() == 200) {

                //dd($response);
                $body = $response->getBody();

                // Read contents of the body
                $pdf = $body->getContents();
                //dd($pdf);
                //dd($response->getStatusCode());
                return $pdf;
            } else
                return $response->getStatusCode();

        }
    }


}
