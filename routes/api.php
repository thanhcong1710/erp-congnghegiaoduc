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
    Route::prefix('auth')->group(function () {
        Route::post('login', 'AuthController@login');
        Route::post('register', 'AuthController@register');
        Route::post('forgot-password', 'AuthController@forgotPassword');
        Route::post('reset-password', 'AuthController@resetPassword');
    });
    Route::group(['middleware' => 'jwt.auth'], function ($router) {
        Route::get('menu', 'MenuController@index');
        Route::post('auth/logout', 'AuthController@logout');
        Route::prefix('user')->group(function () {
            Route::post('update-info', 'UserController@updateInfo');
            Route::post('change-password', 'UserController@changePassword');
        });
        Route::prefix('roles')->group(function () {
            Route::get('{id}', 'RolesController@info');
            Route::post('create', 'RolesController@create');
            Route::post('delete', 'RolesController@delete');
            Route::post('list', 'RolesController@list');
            Route::post('permissions', 'RolesController@permissions');
        });
    });
});
