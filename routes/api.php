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
    Route::prefix('export')->group(function () {
        Route::get('import/{import_id}', 'ExportController@import');
    });
    Route::group(['middleware' => 'jwt.auth'], function ($router) {
        Route::get('menu', 'MenuController@index');
        Route::post('auth/logout', 'AuthController@logout');
        Route::prefix('system')->group(function () {
            Route::get('branches', 'SystemController@getBranches');
            Route::get('roles', 'SystemController@getRoles');
            Route::get('sources', 'SystemController@getSources');
            Route::get('source_detail', 'SystemController@getSourceDetail');
        });
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
        Route::prefix('users')->group(function () {
            Route::post('list', 'UserController@list');
            Route::post('add', 'UserController@add');
            Route::get('{user_id}', 'UserController@info');
            Route::post('update/{user_id}', 'UserController@update');
            Route::get('get-data/users-manager', 'UserController@getUsersManager');
        });
        Route::prefix('crm')->group(function () {
            Route::post('imports/list', 'ImportsController@list');
            Route::post('imports/upload', 'ImportsController@upload');
            Route::post('imports/assign', 'ImportsController@assign');
        });
    });
});
