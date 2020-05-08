<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Endpoint SSO-Route
| Routes secured by middleware
| Endpoint upload-route
|
*/

use App\Services\AuthHandler;

if(class_exists(AuthHandler::class))
    $login = app()->make('SystemService')->authorize()->global->login_route;
//Endpoint
Route::get($login, 'DashboardController@login')->name('login');


Route::middleware('gdpr')->group(function () {

Route::get('/', 'DashboardController@index')->name('home');
Route::get('/status','DashboardController@status')->name('home_status');

//Download completed gdpr-request
Route::get('/download/{id}', 'DashboardController@download')->name('download');

//Delete completed gdpr-request
Route::get('/delete/{id}', 'DashboardController@delete')->name('delete');

//Override failed gdpr-request
Route::get('/override/{id}', 'DashboardController@override')->name('override');

//Initiate a Request
Route::post('/search', 'SearchController@search')->name('search');

//Callbacks
Route::get('/oauth/callback', 'CallbackController@callback');
Route::get('/plugins', 'PluginController@run');

//Plugin configuration
Route::get('/plugin_configuration', 'PluginController@index')->name('plugin');
Route::post('/plugin_configuration/{plugin}', 'PluginController@update')->name('plugin_update');

//Email registrar
Route::get('/emailregistrar/{id}', 'EmailController@sendEmail')->name('send');

});

//File upload
Route::get('/upload/{id}', 'FileController@index')->name('upload');
Route::post('/store', 'FileController@store')->name('store');

//**********************************************************************************************
//Testing routes - only active in local development enviroment
//**********************************************************************************************

Route::get('/dev-delete/{id}', 'DashboardController@dev_delete')->name('dev_delete');
Route::get('/dev-delete-raw/', 'DashboardController@dev');

Route::get('/sign', 'TestController@sign');

Route::get('/test', 'DashboardController@test')->name('test');
Route::get('/php', 'DashboardController@phpinfo')->name('php');

//Otrs-test
Route::get('/otrs', 'TestController@test_otrs');

//ini
//Route::get('/ini', 'TestController@plugin_ini');
Route::get('/ini', 'TestController@ini');
