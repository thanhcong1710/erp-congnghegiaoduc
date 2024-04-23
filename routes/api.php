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
            Route::get('branches', 'SystemController@getAllBranches');
            Route::get('roles', 'SystemController@getRoles');
            Route::get('sources', 'SystemController@getSources');
            Route::get('source_detail', 'SystemController@getSourceDetail');
            Route::get('provinces', 'SystemController@getProvinces');
            Route::get('jobs', 'SystemController@getJobs');
            Route::get('methods', 'SystemController@getMethods');
            Route::get('/provinces/{province_id}/districts', 'SystemController@getDistrictsByProvice');
            Route::get('branches-has-user', 'SystemController@getBranchesHasUser');
            Route::get('products', 'SystemController@getProducts');
            Route::get('shifts', 'SystemController@getShifts');
            Route::get('rooms/{branch_id}', 'SystemController@getRooms');
            Route::get('cms/{branch_id}', 'SystemController@getCMs');
            Route::get('teachers/{branch_id}', 'SystemController@getTeachers');
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
            Route::get('get-data/all', 'UserController@getAllUsers');
        });
        Route::prefix('crm')->group(function () {
            Route::post('imports/list', 'ImportsController@list');
            Route::post('imports/upload', 'ImportsController@upload');
            Route::post('imports/assign', 'ImportsController@assign');
            Route::post('parents/list', 'ParentsController@list');
            Route::post('parents/validate_phone', 'ParentsController@validatePhone');
            Route::post('parents/overwrite', 'ParentsController@overwrite');
            Route::post('parents/validate_c2c_phone', 'ParentsController@validateC2CPhone');
            Route::post('parents/add', 'ParentsController@add');
            Route::get('parents/show/{parent_id}', 'ParentsController@show');
            Route::post('parents/update', 'ParentsController@update');
            Route::post('parents/change_status', 'ParentsController@changeStatus');
            Route::post('parents/assign', 'ParentsController@assign');
            Route::post('parents/update_next_care_date', 'ParentsController@updateNextCareDate');
            Route::get('parents/get_logs/{parent_id}', 'ParentsController@getLogs');
            Route::get('care/get_all_data/{parent_id}', 'ParentCareController@getAllDataByParent');
            Route::post('care/add', 'ParentCareController@add');
            Route::post('parents/send_sms', 'ParentsController@sendSms');
            Route::post('students/add', 'StudentsController@add');
            Route::get('students/get_all_data/{parent_id}', 'StudentsController@getAllDataByParent');
            Route::post('students/checkin', 'StudentsController@checkin');
            Route::post('parents/assign_list', 'ParentsController@assignList');
        });

        Route::prefix('lms')->group(function () {
            Route::post('checkin/list', 'CheckinController@list');
            Route::post('checkin/student/checked', 'CheckinController@studentChecked');
            Route::post('checkin/upgrade', 'CheckinController@studentUpgrade');
            Route::post('students/list', 'StudentsController@list');
            Route::get('students/show/{student_id}', 'StudentsController@show');
            Route::post('students/update', 'StudentsController@update');
            Route::post('students/search-contract', 'StudentsController@searchContract');
            Route::post('contracts/load-tuition-fee', 'ContractsController@loadTuitionFee');
            Route::post('contracts/load-discount-code', 'ContractsController@loadDiscountCode');
            Route::post('contracts/check-coupon', 'ContractsController@checkCoupon');
            Route::post('contracts/add', 'ContractsController@add');
            Route::post('contracts/list', 'ContractsController@list');
            Route::post('contracts/delete', 'ContractsController@delete');
            Route::get('contracts/show/{contract_id}', 'ContractsController@show');
            Route::post('contracts/update', 'ContractsController@update');
            Route::post('accounting/charges/list', 'ChargesController@list');
            Route::post('accounting/waitcharges/list', 'ChargesController@waitchargesList');
            Route::post('accounting/charges/add', 'ChargesController@add');
            Route::post('enrolments/load-classes', 'EnrolmentsController@loadClasses');
            Route::get('enrolments/info-class/{class_id}', 'EnrolmentsController@getClassInfo');
            Route::post('enrolments/get-students-add', 'EnrolmentsController@getStudentsAdd');
            Route::post('enrolments/add-student', 'EnrolmentsController@addStudent');
        });

        Route::prefix('settings')->group(function () {
            Route::post('classes/load-classes', 'ClassesController@loadClasses');
            Route::post('classes/save', 'ClassesController@save');
            Route::get('classes/info-config/{class_id}', 'ClassesController@infoConfig');
        });
    });
});
