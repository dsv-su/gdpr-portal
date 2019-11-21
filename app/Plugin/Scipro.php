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
            private $client_id;
            private $client_secret;
            private $auth_code;
            private $redirect_uri;

            public function __construct()
            {
                $this->client_id = config('services.scipro-dev.client_id');
                $this->client_secret = config('services.scipro-dev.client_secret');
                $this->auth_code = null;
                $this->redirect_uri = config('services.scipro-dev.redirect_uri');
            }

            public function auth()
            {
                //Scipro dev authregistration
                $client_id = 'c23a3055-046c-11ea-8a09-005056ab682e';
                $client_secret = 'xaes9gethaethie5Rayae9cheicah5as';
                $auth_code = null;
                $redirect_uri = 'http://localhost:8080/oauth/callback';

                // If we have no access token or refresh token, we need to get user consent to obtain one
                $auth_url = 'https://toker.dsv.su.se/authorize?'.http_build_query([
                        'client_id' => $client_id,
                        'redirect_uri' => $redirect_uri,
                        'response_type' => 'code',
                        'scope' => '',
                        'access_type' => '',
                    ]);

                // Redirect to authorization endpoint
                header('Location: '.$auth_url);

                exit;
            }



            public function gettoken()
        {

            //Scipro dev authregistration
            $client_id = 'c23a3055-046c-11ea-8a09-005056ab682e';
            $client_secret = 'xaes9gethaethie5Rayae9cheicah5as';
            $redirect_uri = 'http://localhost:8080/oauth/callback';

            // Authorization client - this is used to request OAuth access tokens
            $reauth_client = new Client([
                // URL for access_token request
                'base_uri' => 'https://toker.dsv.su.se/exchange',
                // 'debug' => true,
            ]);
            $reauth_config = [
                'code' => $_GET['code'],
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
            ];
            $grant_type = new AuthorizationCode($reauth_client, $reauth_config);
            $refresh_grant_type = new RefreshToken($reauth_client, $reauth_config);
            $oauth = new OAuth2Middleware($grant_type, $refresh_grant_type);
            //$oauth->setTokenPersistence($token_storage);
            $stack = HandlerStack::create();
            $stack->push($oauth);
            // This is the normal Guzzle client
            $client = new Client([
                'handler' => $stack,
                'auth'    => 'oauth',
            ]);
            //
            $id = Cache::pull('search'); //Pull and destroy cache
            $endpoint_url = 'https://scipro-dev.dsv.su.se/gdpr/report?identity='.$id;
            $response = $client->get($endpoint_url);
            if ($response->getStatusCode() == 200) {
                //If response == 200 then ->
                // update status
                // get all response headers
                // get body
                // sore file

                // Get all of the response headers.
                foreach ($response->getHeaders() as $name => $values) {
                   // echo $name . ': ' . implode(', ', $values) . "<br>";
                }
                $body = $response->getBody();

                // Read contents of the body
                $zip = $body->getContents();
                //Store file on disk

                Storage::disk('public')->put(Cache::get('request').'_scipro-dev.zip', $zip);
                return 200;
            }
            else return 401;

        }
}
