<?php

/*
 * User Controller Routes
 *
 */

$api->get('/users/all', 'User\UserController@show');
$api->get('/users/chl', 'User\UserController@chl');
$api->get('/users/one', 'User\UserController@getAuthenticatedUser');
$api->get('/user/permission', 'User\UserController@getUserPermission');
$api->get('/users/basic', 'User\UserController@getUserBasic');
$api->get('/users/find', 'User\UserController@getUserFind');
$api->post('/users/save', 'User\UserController@saveUser');
$api->post('/users/bank', 'User\UserController@saveBank');
$api->get('/users/bank', 'User\UserController@getBank');
$api->get('/users/bankl', 'User\UserController@getBankL');
$api->post('/users/cash', 'User\UserController@saveCash');
$api->get('/users/cash', 'User\UserController@getCash');