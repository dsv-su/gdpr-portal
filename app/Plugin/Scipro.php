<?php

namespace App\Plugin;

use kamermans\OAuth2\GrantType\AuthorizationCode;
use kamermans\OAuth2\GrantType\RefreshToken;
use kamermans\OAuth2\OAuth2Middleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Cache;

class Scipro
{
            private $auth_code, $auth_url;
            private $reauth_client, $reauth_config, $grant_type, $refresh_grant_type, $oauth;
            private $stack, $client;
            private $id, $endpoint_url, $response;
            private $body, $zip;
            protected $code, $case;

            public function __construct($code, $case)
            {
                $this->auth_code = null;
                $this->code = $code;
                $this->case = $case;
            }

            public function auth($auth_url, $client_id, $redirect_uri)
            {
                // If we have no access token or refresh token, we need to get user consent to obtain one
                $this->auth_url = $auth_url.'?'.http_build_query([
                        'client_id' => $client_id,
                        'redirect_uri' => $redirect_uri,
                        'response_type' => 'code',
                        'scope' => '',
                        'access_type' => '',
                        //'principal' => 'rydi5898@dsv.su.se',
                        //'entitlement' => 'dsv-user:gdpr',
                    ]);

                // Redirect to authorization endpoint
                header('Location: '.$this->auth_url);

                exit;
            }


            public function gettoken($base_uri, $client_id, $client_secret, $redirect_uri, $endpoint_url)
        {
            // Authorization client - this is used to request OAuth access tokens
            $this->reauth_client = new Client([
                // URL for access_token request
                'base_uri' => $base_uri,
                // 'debug' => true,
            ]);
            $this->reauth_config = [
                'code' => $this->code,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
            ];
            $this->grant_type = new AuthorizationCode($this->reauth_client, $this->reauth_config);
            $this->refresh_grant_type = new RefreshToken($this->reauth_client, $this->reauth_config);
            $this->oauth = new OAuth2Middleware($this->grant_type, $this->refresh_grant_type);

            $this->stack = HandlerStack::create();
            $this->stack->push($this->oauth);
            // This is the normal Guzzle client
            $this->client = new Client([
                'handler' => $this->stack,
                'auth'    => 'oauth',
            ]);
            //
            $this->id = $this->case->request_uid;

            $this->endpoint_url = $endpoint_url. '=' . $this->id;

            try {
                $this->response = $this->client->get($this->endpoint_url);
            }
            catch (\Exception $e) {
                /**
                If there is an exception; Client error;
                 */
                if ($e->hasResponse()) {
                    $response = $e->getResponse();

                    return $response->getStatusCode();

                }
            }

            //Processing response from Scipro
            if($this->response) {
                if ($this->response->getStatusCode() == 200) {
                    //If response == 200 then ->
                    $this->body = $this->response->getBody();
                    // Read contents of the body
                    $this->zip = $this->body->getContents();

                    return $this->zip;
                } else {
                    return $this->response->getStatusCode();
                }

            }

        }
}
