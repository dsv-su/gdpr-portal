<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /*
    public function signin(Request $request)
    {
        // Initialize the OAuth client
        //Oauth2 to toker.dsv.su.se
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => 'c23a3055-046c-11ea-8a09-005056ab682e',
            'clientSecret'            => 'xaes9gethaethie5Rayae9cheicah5as',
            'redirectUri'             => 'http://localhost:8080/oauth/callback',
            'urlAuthorize'            => 'https://toker.dsv.su.se/authorize',
            'urlAccessToken'          => 'https://toker.dsv.su.se/exchange',
            'urlResourceOwnerDetails' => '',
            'scopes'                  => ''
        ]);

        // Generate the auth URL
        $authorizationUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in response
        // $_SESSION['oauth_state'] = $oauthClient->getState();
        $sstate = $oauthClient->getState();
        $request->session()->put('oauth_state', $sstate);
        //dd($request->session()->get('oauth_state'));
        // Redirect to authorization endpoint
        header('Location: '.$authorizationUrl);

        exit();
    }

    public function gettoken(Request $request)
    {

        //dd('State_', $_GET['state'], 'Session:', $request->session()->get('oauth_state'));
        // Authorization code should be in the "code" query param
        if (isset($_GET['code'])) {
            // Check that state matches
            //if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth_state'])) {
            /*
                        if (empty($_GET['state']) || ($_GET['state'] !== $request->session()->get('oauth_state'))) {
                                echo 'State:'.$_GET['state'].' Session:'.$request->session()->get('oauth_state');
                                exit('State provided in redirect does not match expected value.');
                            }

                        // Clear saved state
                        unset($_SESSION['oauth_state']);
            */
    /*
        // Initialize the OAuth client
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => 'c23a3055-046c-11ea-8a09-005056ab682e',
            'clientSecret' => 'xaes9gethaethie5Rayae9cheicah5as',
            'redirectUri' => 'http://localhost:8080/oauth/callback',
            'urlAuthorize' => 'https://toker.dsv.su.se/authorize',
            'urlAccessToken' => 'https://toker.dsv.su.se/exchange',
            'return_url' => 'http://localhost:8080/oauth/callback',
            'urlResourceOwnerDetails' => '',
            'scopes' => ''
        ]);

        try {
            // Make the token request
            $accessToken = $oauthClient->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            echo 'Access token: ' . $accessToken->getToken();
        } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            exit('ERROR getting tokens: ' . $e->getMessage());
        }
        exit();
        }
        elseif (isset($_GET['error'])) {
            exit('ERROR: '.$_GET['error'].' - '.$_GET['error_description']);
        }
    }
    */
}

