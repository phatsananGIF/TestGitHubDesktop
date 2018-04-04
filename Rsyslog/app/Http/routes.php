<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('log','RsyslogController@index');
Route::post('/log/viewfile','RsyslogController@viewfile');

Route::get('/log/download/{id}','RsyslogController@downloadfile');
Route::get('/log/delete/{id}','RsyslogController@deletefile');
//Route::post('/log/show','RsyslogController@show');
//Route::get('testread','RsyslogController@testread');
