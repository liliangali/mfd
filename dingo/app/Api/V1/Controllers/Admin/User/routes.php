<?php

/*
 * User Controller Routes
 *
 */

$api->get('/mfd/users/all', 'User\UserController@all');
$api->post('/mfd/users/list', 'User\UserController@userList');
$api->get('/mfd/users/basic', 'User\UserController@getUserBasic');
$api->get('/mfd/users/find', 'User\UserController@getUserFind');
$api->post('/mfd/users/save', 'User\UserController@saveUser');
