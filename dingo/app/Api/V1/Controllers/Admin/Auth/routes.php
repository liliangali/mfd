<?php

/*
 * Auth Controller Routes
 *
 */


$api->post('/mfd/login', 'Auth\AuthController@authenticate');
$api->any('/auth/img/{oid}/{rid?}', 'Auth\AuthController@img')->middleware(['api.sign']);
$api->any('/auth/appid/{remark}/{all?}', 'Auth\AuthController@appid');
$api->any('/auth/test/', 'Auth\AuthController@test');
$api->post('/auth/register', 'Auth\AuthController@register');
$api->post('/auth/resetPassword', 'Auth\AuthController@resetPassword');
$api->post('/mfd/users/discount', 'Auth\AuthController@saveDicount');
$api->get('/mfd/users/discount', 'Auth\AuthController@getDicount');