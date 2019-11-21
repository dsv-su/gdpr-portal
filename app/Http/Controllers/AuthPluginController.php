<?php

namespace App\Http\Controllers;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Storage;
use kamermans\OAuth2\GrantType\AuthorizationCode;
use kamermans\OAuth2\GrantType\RefreshToken;
use kamermans\OAuth2\Persistence\FileTokenPersistence;
use kamermans\OAuth2\OAuth2Middleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;


class AuthPluginController extends Controller
{
    //This is a test script to connect to Scipro for GDPR extraction
    //Only for testing purposes!!

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

        exit();


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
        $endpoint_url = 'https://scipro-dev.dsv.su.se/gdpr/report?identity=rydi5898@su.se';
        $response = $client->get($endpoint_url);
        if ($response->getStatusCode() == 200) {
            //dd('Status:', $response->getStatusCode());

            // Get all of the response headers.
            foreach ($response->getHeaders() as $name => $values) {
                echo $name . ': ' . implode(', ', $values) . "<br>";
            }
            $body = $response->getBody();

            // Read contents of the body
            $zip = $body->getContents();
            //Store file on disk
            Storage::put('ryan.zip', $zip);
            return 'Done';
        }
        else return 'User not found';

        dd('H');
    }
    public function getMoodle()
    {

        $client = new Client();
        try {
            $response = $client->get('https://ilearn2test.dsv.su.se/gdpr/do_fetch.php?op=1&username=tdsv');
        }
        catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();

        }
        if($response->getStatusCode()==200) {
            $body = $response->getBody();

            // Read contents of the body
            $zip = $body->getContents();
            Storage::put('ryan_moodle.zip', $zip);
            dd('Done');
        }
        else
        dd('User not found');

  /*
        //$url = 'https://ilearn2test.dsv.su.se/gdpr';
        //$moodle = new Client();
        //$myFile = fopen('tdsv.zip', 'w') or die('Problems');
        //$response = $moodle->get("$url/do_fetch.php".'?op=1&username=tdsv', ['save_to' => $myFile]);
        $response = $moodle->get("$url/do_fetch.php".'?op=1&username=tdsv');
        $body = $response->getBody();

        // Read contents of the body
        $zip = $body->getContents();
        Storage::put('ryan_moodle.zip', $zip);
        return $response->getStatusCode(); # 200

            //Store file on disk
        //Storage::put('ryan_moodle.zip', $data);
        //return 'Done';
/*
        $client = new Client();
        $response = $client->request('GET', 'https://ilearn2test.dsv.su.se/do_export.php?op=1&username=tdsv');
        */
        //return $response->getStatusCode(); # 200


    }

}
