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

    Route::get('datasets', 'DatasetsController@index');
    Route::post('datasets/ajax', 'DatasetsController@ajax');
    Route::get('datasets/create', 'DatasetsController@create');
    Route::post('datasets/create', 'DatasetsController@add');
    Route::get('datasets/{id}/edit', 'DatasetsController@edit');
    Route::post('datasets/{id}/edit', 'DatasetsController@update');
    Route::delete('datasets/{id}', 'DatasetsController@delete');
});
