<?php

namespace App\Plugin;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class Otrs extends GenericPlugin
{
    private $response;

    public function getOtrs()
    {
        $client = new Client(['cookies' => true]);

        try {
            $this->response = $client->post($this->plugin->endpoint_url, [
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
                $this->response = $e->getResponse();

                return $this->response->getStatusCode();

            }
        }

        //Processing response
        if ($this->response) {
            $body = $this->response->getBody();
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
                return 'not_found';
            }
            else {
                $files = count($json->tickets); //Number of files
                //dd($files);
                $progress = 15;
                            foreach ($json->tickets as $value) {

                                        try {
                                            $this->response = $client->post($this->plugin->endpoint_url, [
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
                                                $this->response = $e->getResponse();

                                                return $this->response->getStatusCode();

                                                }

                                         }

                                    //Processing response from Otrs
                                    if ($this->response) {
                                        if ($this->response->getStatusCode() == 200) {

                                            //dd($response);
                                            $body = $this->response->getBody();

                                            // Read contents of the body
                                            $pdf = $body->getContents();
                                            //Temp storing pdf to disk
                                            Storage::disk('public')->put($this->case->case_id . '/raw/'.$this->plugin->name. '/' . $value->TicketID . '.pdf', $pdf);

                                        } else
                                        {
                                            $this->status->setDownloadStatus(0);
                                            switch($this->response->getStatusCode())
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
                                            return 'ok';
                                        }


                                    }
                            }
            }
        }
        $this->status->setDownloadStatus(100);
        return 'ok';
    }
}
