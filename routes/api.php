<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'api'
], function () {

    Route::post('login', 'ApiController@login');
    Route::post('gmail/login', 'ApiController@loginWithGmail');
    Route::post('register', 'ApiController@register');
    Route::post('refreshfbtoken', 'ApiController@refreshFbToken');

});


Route::group(['namespace' => 'api', 'middleware' => 'auth:api'
], function () {

    Route::post('getAllMaker', 'MechanicController@getAllMaker');
    Route::get('all-vehicles', 'VehicleController@getAllVehicles');
    Route::post('getAllMechanic', 'MechanicController@getAllMechanic');
    Route::post('sendMechanicLocation', 'MechanicController@sendMechanicLocation');
    Route::post('changeMechanicLocation', 'MechanicController@changeMechanicLocation');


    Route::post('saveNotifClientMainRequest', 'RequestEmergencyController@saveNotifClientMainRequest');
    Route::post('getRemainingTime', 'RequestEmergencyController@getRemainingTime');
    Route::post('sendProcessStatus', 'RequestEmergencyController@sendProcessStatus');
    Route::post('getEmergenciesMechanic', 'RequestEmergencyController@getEmergenciesMechanic');
    Route::post('getEmergenciesDriver', 'RequestEmergencyController@getEmergenciesDriver');

    Route::post('getAllNotif', 'NotificationController@getAllNotif');
    Route::post('getAllHisto', 'NotificationController@getAllHisto');
    Route::post('getNotifInfo', 'NotificationController@getNotifInfo');
    Route::post('notifRequestFromCancel', 'NotificationController@notifRequestFromCancel');
    Route::post('notifRequestFromAccept', 'NotificationController@notifRequestFromAccept');


    Route::post('getCurrentStatus', 'ApiController@getCurrentStatus');
    Route::post('setRating', 'ApiController@setRating');

});
