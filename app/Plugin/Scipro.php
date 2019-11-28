<?php

namespace App\Plugin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use kamermans\OAuth2\GrantType\AuthorizationCode;
use kamermans\OAuth2\GrantType\RefreshToken;
use kamermans\OAuth2\Persistence\FileTokenPersistence;
use kamermans\OAuth2\OAuth2Middleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Cache;

class Scipro
{
            private $client_id, $client_secret;
            private $auth_code, $redirect_uri, $auth_url;
            private $reauth_client, $reauth_config, $grant_type, $refresh_grant_type, $oauth;
            private $stack, $client;
            private $id, $endpoint_url, $response;
            private $code, $body, $zip;

            public function __construct($code)
            {
                $this->client_id = config('services.scipro-dev.client_id');
                $this->client_secret = config('services.scipro-dev.client_secret');
                $this->auth_code = null;
                $this->redirect_uri = config('services.scipro-dev.redirect_uri');
                $this->code = $code;
            }

            public function auth()
            {
                // If we have no access token or refresh token, we need to get user consent to obtain one
                $this->auth_url = 'https://toker.dsv.su.se/authorize?'.http_build_query([
                        'client_id' => $this->client_id,
                        'redirect_uri' => $this->redirect_uri,
                        'response_type' => 'code',
                        'scope' => '',
                        'access_type' => '',
                    ]);

                // Redirect to authorization endpoint
                header('Location: '.$this->auth_url);

                exit;
            }


            public function gettoken()
        {
            // Authorization client - this is used to request OAuth access tokens
            $this->reauth_client = new Client([
                // URL for access_token request
                'base_uri' => 'https://toker.dsv.su.se/exchange',
                // 'debug' => true,
            ]);
            $this->reauth_config = [
                'code' => $this->code,
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'redirect_uri' => $this->redirect_uri,
            ];
            $this->grant_type = new AuthorizationCode($this->reauth_client, $this->reauth_config);
            $this->refresh_grant_type = new RefreshToken($this->reauth_client, $this->reauth_config);
            $this->oauth = new OAuth2Middleware($this->grant_type, $this->refresh_grant_type);
            //$oauth->setTokenPersistence($token_storage);
            $this->stack = HandlerStack::create();
            $this->stack->push($this->oauth);
            // This is the normal Guzzle client
            $this->client = new Client([
                'handler' => $this->stack,
                'auth'    => 'oauth',
            ]);
            //
            $this->id = Cache::pull('search'); //Pull and destroy cache
            $this->endpoint_url = 'https://scipro-dev.dsv.su.se/gdpr/report?identity='.$this->id;
            $this->response = $this->client->get($this->endpoint_url);
            if ($this->response->getStatusCode() == 200) {
                //If response == 200 then ->
                $this->body = $this->response->getBody();
                // Read contents of the body
                $this->zip = $this->body->getContents();

                return $this->zip;
            }


        }
}
