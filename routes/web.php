<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Endpoint
Route::get('/', 'DashboardController@index')->name('home');
Route::get('/status','DashboardController@status')->name('home_status');

//Download completed gdpr-request
Route::get('/download/{id}', 'DashboardController@download')->name('download');

//Delete completed gdpr-request
Route::get('/delete/{id}', 'DashboardController@delete')->name('delete');
Route::get('/dev-delete/{id}', 'DashboardController@dev_delete')->name('dev_delete');

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

//File upload
Route::get('/upload/{id}', 'FileController@index')->name('upload');
Route::post('/store', 'FileController@store')->name('store');



//**********************************************************************************************
//Testing routes - to be removed in production
//**********************************************************************************************
Route::get('/sign', 'TestController@sign');
Route::get('/dev-delete-raw/', 'DashboardController@dev');
Route::get('/test', 'DashboardController@test')->name('test');
Route::get('/php', 'DashboardController@phpinfo')->name('php');

Route::get('/video/', 'TestController@video');

//Otrs-test
Route::get('/otrs', 'TestController@test_otrs');

//ini
//Route::get('/ini', 'TestController@plugin_ini');
Route::get('/ini', 'TestController@ini');
