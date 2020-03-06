<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use kamermans\OAuth2\GrantType\AuthorizationCode;
use kamermans\OAuth2\GrantType\RefreshToken;
use kamermans\OAuth2\OAuth2Middleware;

class Toker extends Model
{
    private $auth_url, $accessToken, $provider;

    public function __construct($case, $plugin, $status)
    {
        $this->case = $case;
        $this->plugin = $plugin;
        $this->status = $status;
    }

    public function auth()
    {
        // If we have no access token or refresh token, we need to get user consent to obtain one
        $this->auth_url = $this->plugin->auth_url.'?'.http_build_query([
                'client_id' => $this->plugin->client_id,
                'redirect_uri' => $this->plugin->redirect_uri,
                'response_type' => 'code',
                'scope' => '',
                'access_type' => '',
                //'principal' => 'rydi5898@dsv.su.se',
                //'entitlement' => 'dsv-user:gdpr',
            ]);

        $this->status->auth = 1;
        $this->status->save();

        // Redirect to authorization endpoint
        header('Location: '.$this->auth_url);

        exit;
    }

    public function getToken($code)
    {

    }
}
