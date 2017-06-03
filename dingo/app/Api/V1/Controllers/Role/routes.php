<?php

// Role Controller Routes

$api->get('/role/role_type/{grade}', 'Role\RoleController@getRoleType');
$api->get('/role/region', 'Role\RoleController@region');
$api->post('/role/pcode', 'Role\RoleController@pcode');
