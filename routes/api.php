<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'api'], function ($router) {
    Route::get('bbb/create', 'BigBluButtonController@createRoom');
    Route::get('bbb/join', 'BigBluButtonController@joinRoom');
    Route::get('bbb/info', 'BigBluButtonController@getRoomInfo');
    Route::get('bbb/list', 'BigBluButtonController@getListRoom');
    Route::get('bbb/end', 'BigBluButtonController@endRoom');
    Route::get('bbb/record', 'BigBluButtonController@getRecords');
    Route::get('bbb/record/delete', 'BigBluButtonController@deleteRecord');
    Route::get('testMail', 'AuthController@testMail');
    Route::get('viewMail', 'AuthController@viewMail');
    Route::get('account/activate/{key}', 'AuthController@activeAccount');

    Route::prefix('auth')->group(function () {
        Route::post('login', 'AuthController@login');
        Route::post('register', 'AuthController@register');
        Route::post('forgot-password', 'AuthController@forgotPassword');
        Route::post('reset-password', 'AuthController@resetPassword');
    });
    Route::prefix('law')->group(function () {
        Route::post('list', 'LawController@list');
        Route::post('item/detail', 'LawController@getItemDetail');
    });
    Route::group(['middleware' => 'jwt.auth'], function ($router) {
        Route::post('auth/logout', 'AuthController@logout');
        Route::prefix('user')->group(function () {
            Route::post('update-info', 'UserController@updateInfo');
            Route::post('change-password', 'UserController@changePassword');
        });
        Route::prefix('video-call')->group(function () {
            Route::post('create', 'VideoCallController@create');
            Route::post('join', 'VideoCallController@join');
        });
    });
});
