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

        Route::post('message', 'BroadcastController@sendMessage');

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
        Route::post('payments/updatePayment', 'PaymentsController@updatePayment');
        Route::post('payments/updatePaymentDate', 'PaymentsController@updatePaymentDate');

        Route::post('payments/removePayment', 'PaymentsController@removePayment');

        //Search Payments by date
        Route::post('payments/searchByDate', 'PaymentsController@searchPaymentByDate');
        Route::post('payments/forecast/searchByDate', 'PaymentsController@searchPaymentForecastByDate');

        Route::post('payments/submit', 'PaymentsController@createPayments');

        Route::post('/logout', 'APIController@logout');

        Route::get('/expenses/monthlyExpenses', 'MonthlyExpensesController@getMonthlyExpenses');
        Route::post('/expenses/monthly/new', 'MonthlyExpensesController@create');
        Route::post('/expenses/receipt', 'ExpensesController@saveReceipt');
        Route::post('/expenses/new', 'ExpensesController@create');
        Route::post('/expenses/update', 'ExpensesController@updateExpense');
        Route::post('/expenses/get','ExpensesController@getExpenses');
        Route::post('/expenses/forecast/searchByDate','ExpensesController@searchExpenseForecastByDate');

        Route::post('/schedule/createEvent', 'ScheduleController@createEvent');
        Route::post('/schedule/updateEvent', 'ScheduleController@updateEvent');
        Route::post('/schedule/removeEvent', 'ScheduleController@removeEvent');
        Route::post('/schedule/getEvents','ScheduleController@getEvents');


        Route::post('/clients/search/birthdays','ClientController@getBirthdaysClient');

        Route::post('/reports/monthlyBalance','ReportController@monthlyBalanceReport');
        Route::post('/reports/conciliation','ReportController@conciliationReport');

        Route::get('/config','ConfigController@getConfigs');
        Route::POST('/sumup/authorize','SumupController@authorization');
    });


});





