<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::group(['middleware' => 'auth'], function () {
    Route::get('institutions', 'InstitutionsController@index');
    Route::post('institutions/ajax', 'InstitutionsController@ajax');
    Route::get('institutions/create', 'InstitutionsController@create');
    Route::post('institutions/create', 'InstitutionsController@add');
    Route::get('institutions/{id}/edit', 'InstitutionsController@edit');
    Route::post('institutions/{id}/edit', 'InstitutionsController@update');
    Route::delete('institutions/{id}', 'InstitutionsController@delete');
});
