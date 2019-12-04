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
Route::get('zip', 'DiskController@unzip')->name('test');
Route::get('mzip', 'DiskController@index')->name('zip');
Route::get('/download/{id}', 'DiskController@download')->name('download');
//Request
Route::post('/search', 'SearchController@search')->name('search');
//Callback plugin 1: Scipro-dev
Route::get('/oauth/callback', 'CallbackSciproController@callbackScipro');


Route::get('/home', function () {
    return view('search.home');
})->name('new_search');

//Request
//Route::post('/search', 'SearchController@search')->name('search');

//Request plugin 2: Moodle-dev
Route::get('/moodle', 'SearchController@callMoodle');





//Plugin 1 test
Route::get('/signin', 'AuthController@signin');
//Plugin 2 test
Route::get('/guzzle', 'AuthPluginController@auth');
//Route::get('/oauth/callback', 'AuthPluginController@gettoken');
//Plugin 3 test
//Route::get('/moodle', 'AuthPluginController@getMoodle');
//Shibboleth emulated login
//Route::redirect('/login', '/shibboleth-login')->name('login');
//Route::redirect('/login', 'login.dsv.se')->name('login');
//Authenticated routes for Shibboleth -->
//Route::middleware('auth')->group(function() {
    //Route::post('/search/{gdpr_id}', 'SearchController@search')->name('search');
    Route::post('/result/{id}', 'SciproDevController@result')->name('scipro_dev');
    Route::get('/authorize', 'SciproDevController@gettoken');
//});
