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
Route::get('/status','DashboardController@status');

//Download completed gdpr-request
Route::get('/download/{id}', 'DashboardController@download')->name('download');

//Delete completed gdpr-request
Route::get('/delete/{id}', 'DashboardController@delete')->name('delete');
Route::get('/dev-delete/{id}', 'DashboardController@dev_delete')->name('dev_delete');

//Initiate a Request
Route::post('/search', 'SearchController@search')->name('search');

//Callbacks
Route::get('/oauth/callback', 'CallbackSciproController@callbackScipro');

//Finished request
Route::get('/finished', 'DashboardController@request_end')->name('finished');
//Route::get('email', 'EmailController@sendEMail');

//Test
Route::get('/t', 'DashboardController@testview')->name('tview');
Route::get('/test', 'DashboardController@test')->name('test');
Route::get('/php', 'DashboardController@phpinfo')->name('php');

//Scipro dev test
//Route::get('/scipro', 'TestController@test_scipro');
//Route::get('/oauth/callback', 'TestController@callbackScipro');

//Moodle-test
//Route::get('/moodle', 'TestController@test_moodle');
