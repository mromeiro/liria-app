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

    //Login
    Route::post('login', 'APIController@login');

    Route::group(['middleware' => 'jwt-auth'], function () {

        //Recover all clientes in the database
        Route::get('clients', 'ClientController@get');

        //Check if the client is logged
        Route::get('/isLogged', 'APIController@loginCheck');

        //Recover the cliente based on their ID
        Route::get('clients/{clientId}', 'ClientController@getClient');

        //Create a cliente
        Route::post('clients', 'ClientController@create');

        //Create a cliente
        Route::post('clients/update', 'ClientController@update');

        //Create a cliente
        Route::post('clients/search/nameOrCpf', 'ClientController@getClientByNameOrCpf');

        //Upload images
        Route::post('upload','ClientController@uploadPhoto');

        //Get the list of available treatments
        Route::get('treatments', 'TreatmentsController@get');

        //Get the list of available treatments
        Route::post('treatments/update/{clienteId}', 'TreatmentsController@updateTreatment');

        //Create a treatment for a specific cliente along with its payments
        Route::post('treatments/create/{clienteId}', 'TreatmentsController@createTreatmentForClient');

        //Create a treatment for a specific cliente along with its payments
        Route::post('payments/updatePaymentDate', 'PaymentsController@updatePaymentDate');

        //Search Payments by date
        Route::post('payments/searchByDate', 'PaymentsController@searchPaymentByDate');

        Route::post('/logout', 'APIController@logout');
    });


});





