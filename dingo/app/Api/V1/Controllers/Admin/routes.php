<?php

$dir = app_path().'/Api/V1/Controllers/Admin/';
include $dir.'Auth/routes.php';

$api->group(['middleware' => 'jwt.auth'], function($api) use($dir) {
    $api->group(['middleware' => 'api.admin'], function($api) use($dir) {//后台中间件
        include $dir.'User/routes.php';
        include $dir.'Order/routes.php';

    });
});

