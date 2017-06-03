<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */
Route::any("wx","WxController@index");
Route::any("wx/wxthree","WxController@wxthree");
Route::any("notify/{appid}","WxController@notify");
Route::get("wx/wcode","WxController@wcode")->name('wxcode');
Route::get("qrcode","WxToolController@qrcode");
Route::get('/', function () {
    return view('welcome');
});

/*
 * Include  Routes
 */

$api = app('Dingo\Api\Routing\Router');
include app_path().'/Api/V1/routes.php';

/* $api->version('v1', function ($api) { */
/*     $api->group(['namespace' => 'App\Api\V1\Controllers'], function($api) { */
/*         /1* $api->post('/auth/login', 'Auth\AuthController@authenticate'); *1/ */
/*         /1* $api->post('/auth/register', 'Auth\AuthController@register'); *1/ */
/*         include '/var/www/pms_api/app/Api/V1/Auth/routes.php'; */
/*         $api->group(['middleware' => 'jwt.auth'], function($api) { */
/*             $api->get('/users/all', 'User\UserController@show'); */
/*             $api->get('/users/one', 'User\UserController@getAuthenticatedUser'); */
/*             $api->post('/company/add', 'Company\CompanyController@add'); */
/*         }); */
/*     }); */
/* }); */




