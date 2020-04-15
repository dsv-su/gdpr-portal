<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Views / Endpoints
    |--------------------------------------------------------------------------
    |
    | Set your login page, or login routes, here. If you provide a view,
    | that will be rendered. Otherwise, it will redirect to a route.
    |
 */

    'idp_login' => '/login.dsv.su.se', // /Shibboleth.sso/Login
    'idp_logout' => '/Shibboleth.sso/Logout', // /Shibboleth.sso/Logout
    'authenticated' => '/',

    /*
    |--------------------------------------------------------------------------
    | Emulate an IdP
    |--------------------------------------------------------------------------
    |
    | In case you do not have access to your Shibboleth environment on
    | homestead or your own Vagrant box, you can emulate a Shibboleth
    | environment with the help of Shibalike.
    |
    | The password is the same as the username.
    |
    | Do not use this in production for literally any reason.
    |
     */

    'emulate_idp' => env('EMULATE_IDP', false),
    'emulate_idp_users' => [
        'admin' => [
            'Shib-cn' => 'Admin User',
            'Shib-mail' => 'dmc@dsv.su.se',
            'Shib-givenName' => 'Admin',
            'Shib-sn' => 'User',
            'Shib-emplId' => 'admin',
        ],
        'staff' => [
            'Shib-cn' => 'Staff User',
            'Shib-mail' => 'ryan@dsv.su.se',
            'Shib-givenName' => 'Staff',
            'Shib-sn' => 'User',
            'Shib-emplId' => 'staff',
        ],
        'user' => [
            'Shib-cn' => 'Ryan Dias',
            'Shib-mail' => 'ryan@dsv.su.se',
            'Shib-givenName' => 'Ryan',
            'Shib-sn' => 'Dias',
            'Shib-emplId' => 'user',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Server Variable Mapping
    |--------------------------------------------------------------------------
    |
    | Change these to the proper values for your IdP.
    |
     */

    'entitlement' => 'Shib-isMemberOf', //Shib-isMemberOf

    'user' => [
        // fillable user model attribute => server variable
        'name' => 'Shib-cn', //Shib-cn
        'first_name' => 'Shib-givenName',
        'last_name' => 'Shib-sn', //Shib-sn
        'email' => 'Shib-mail',
        'emplid' => 'Shib-emplId',
    ],

    /*
    |--------------------------------------------------------------------------
    | User Creation and Groups Settings
    |--------------------------------------------------------------------------
    |
    | Allows you to change if / how new users are added
    |
     */

    'add_new_users' => true, // Should new users be added automatically if they do not exist?

    /*
    |--------------------------------------------------------------------------
    | JWT Auth
    |--------------------------------------------------------------------------
    |
    | JWTs are for the front end to know it's logged in
    |
    | https://github.com/tymondesigns/jwt-auth
    | https://github.com/StudentAffairsUWM/Laravel-Shibboleth-Service-Provider/issues/24
    |
     */

    'jwtauth' => env('JWTAUTH', false),
];
