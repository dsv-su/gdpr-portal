<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'scipro-dev' => [
        'client_name' => env('SCIPRO_DEV_CLIENT_NAME'),
        'client_id' => env('SCIPRO_DEV_CLIENT_ID'),
        'auth_url' => env('SCIPRO_DEV_AUTH_URL'),
        'base_uri' => env('SCIPRO_DEV_BASE_URI'),
        'client_secret' => env('SCIPRO_DEV_CLIENT_SECRET'),
        'redirect_uri' => env('SCIPRO_DEV_REDIRECT_URI'),
    ],
    'case' => [
        'start' => env('CASE_START_ID'),
        'ttl' => env('CASE_TTL'),
    ],
    'moodle-test' => [
        'client_name' => env('MOODLE_NAME'),
        'endpoint_uri' => env('MOODLE_URI'),
    ]

];
