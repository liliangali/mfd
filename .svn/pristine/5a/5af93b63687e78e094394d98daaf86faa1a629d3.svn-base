<?php

/*
 * Auth Controller Routes
 *
 */

$api->post('/auth/login', 'Auth\AuthController@authenticate');
$api->post('/auth/alogin', 'Auth\AuthController@aauthenticate');
$api->any('/auth/img/{oid}/{rid?}', 'Auth\AuthController@img')->middleware(['api.sign']);
$api->any('/auth/imgup', 'Auth\AuthController@imgup');
$api->any('/auth/appid/{remark}/{all?}', 'Auth\AuthController@appid');
$api->any('/auth/test/', 'Auth\AuthController@test');
$api->post('/auth/register', 'Auth\AuthController@register');
$api->post('/auth/resetPassword', 'Auth\AuthController@resetPassword');
