<?php

namespace App\Plugin;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class Otrs
{
    protected $case, $plugin, $status;

    public function __construct($case, $plugin, $status)
    {
        $this->case = $case;
        $this->plugin = $plugin;
        $this->status = $status;
    }

    public function getOtrs()
    {
        $client = new Client(['cookies' => true]);

        try {
            $response = $client->post($this->plugin->endpoint_url, [
                'form_params' => [
                    'Action' => 'Login',
                    'RequestedURL' => 'Action=AgentGdprHandler&Subaction=Search&Fulltext=' .$this->case->request_email. '&OutputFormat=JSON',
                    'User' => 'devtest',
                    'Password' => 'hunter2'
                ]
            ]);
            $this->status->setProgressStatus(15);
            $this->status->setDownloadStatus(15);
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
            $body = $response->getBody();
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
            //dd($json);
            if ($json == null)
            {
                return 204;
            }
            else {
                $files = count($json->tickets); //Number of files
                $progress = 15;
                            foreach ($json->tickets as $value) {

                                        try {
                                            $response = $client->post($this->plugin->endpoint_url, [
                                                'form_params' => [
                                                    'Action' => 'GdprTicketPrint',
                                                    'TicketID' => $value->TicketID,
                                                ]
                                            ]);
                                            $progress = $progress + (85/$files);
                                            $this->status->setProgressStatus($progress);
                                            $this->status->setDownloadStatus($progress);
                                            }
                                        catch (\Exception $e) {
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
                                            //Temp storing pdf to disk
                                            Storage::disk('public')->put($this->case->case_id . '/raw/'.$this->plugin->name. '/' . $value->TicketID . '.pdf', $pdf);

                                        } else
                                            return $response->getStatusCode();

                                    }
                            }
            }
        }
        return 200;
    }
}
