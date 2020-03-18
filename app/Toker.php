<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use kamermans\OAuth2\GrantType\AuthorizationCode;
use kamermans\OAuth2\GrantType\RefreshToken;
use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\Persistence\FileTokenPersistence;

class Toker extends Model
{
    private $auth_url, $token_persistence, $oauth, $grant_type;

    public function __construct($system)
    {
        $this->system = $system;
    }

    public function auth()
    {
        // Get code from Toker
        $this->auth_url = $this->system->auth_url.'?'.http_build_query([
                'client_id' => $this->system->client_id,
                'redirect_uri' => $this->system->redirect_uri,
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

    public function getToken($code)
    {
        // Authorization client - this is used to request OAuth access tokens from Toker
        $this->reauth_client = new Client([
            // URL for access_token request
            'base_uri' => $this->system->base_uri,
            // 'debug' => true,
        ]);
        $this->reauth_config = [
            'code' => $code,
            'client_id' => $this->system->client_id,
            'client_secret' => $this->system->client_secret,
            'redirect_uri' => $this->system->redirect_uri,
        ];

        $public_dir = public_path().'/storage/';
        $token_path = $public_dir.'access_token.json';
        $this->token_persistence = new FileTokenPersistence($token_path);
        $this->grant_type = new AuthorizationCode($this->reauth_client, $this->reauth_config);
        $this->refresh_grant_type = new RefreshToken($this->reauth_client, $this->reauth_config);
        $this->oauth = new OAuth2Middleware($this->grant_type, $this->refresh_grant_type);

        return $this->oauth->getAccessToken();
    }
}
