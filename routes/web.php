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

/*
 * /api/X contains all APIs that will be accessed by the front-end
 */
Route::group(['middleware' => ['api'],'prefix' => 'api'], function () {

    Route::post('login', 'APIController@login');

    Route::group(['middleware' => 'jwt-auth'], function () {

        Route::get('clients', 'ClientController@get');
        Route::get('clients/{clientId}', 'ClientController@getClient');
        Route::post('clients', 'ClientController@create');
        Route::post('upload','ClientController@uploadPhoto');

        Route::get('treatments', 'TreatmentsController@get');
        Route::post('treatments/create/{clienteId}', 'TreatmentsController@createTreatmentForClient');

    });
});




