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

Route::group(['namespace' => 'api'], function () {
    Route::post('login', 'ApiController@login');
    Route::post('register', 'ApiController@register');
    Route::post('refreshfbtoken', 'ApiController@refreshFbToken');
    Route::post('getAllMaker', 'ApiController@getAllMaker');
    Route::post('sendMechanicLocation', 'ApiController@sendMechanicLocation');
    Route::post('changeMechanicLocation', 'ApiController@changeMechanicLocation');
    Route::post('saveNotifClientMainRequest', 'ApiController@saveNotifClientMainRequest');
    Route::post('getMechanicInfo', 'ApiController@getMechanicInfo');

    Route::post('getAllNotif', 'ApiController@getAllNotif');
    Route::post('getAllHisto', 'ApiController@getAllHisto');
    Route::post('getNotifInfo', 'ApiController@getNotifInfo');
    Route::post('getRemainingTime', 'ApiController@getRemainingTime');

    Route::post('notifRequestFromCancel', 'ApiController@notifRequestFromCancel');
    Route::post('notifRequestFromAccept', 'ApiController@notifRequestFromAccept');

    Route::post('setRating', 'ApiController@setRating');

});
