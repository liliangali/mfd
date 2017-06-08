<?php

/*
 * Dingo Api Routes
 */

$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\V1\Controllers'], function($api) {
        $dir = app_path().'/Api/V1/Controllers/' ;
        //Auth Controller Routes
        include $dir.'Auth/routes.php';
        // Push Controller Routes
        include $dir.'SendMail/routes.php';
        include $dir.'Good/routes.php';
        // UserGroup Controller Routes
        include $dir.'Fbcategory/routes.php';
        include $dir.'Wx/routes.php';
        include $dir.'Order/routes.php';
        include $dir.'Role/routes.php';

        $api->group(['middleware' => 'jwt.auth'], function($api) {
            $dir = app_path().'/Api/V1/Controllers/';
            // User Controller Routes
            include $dir.'User/routes.php';
            include $dir.'Article/routes.php';
        });
    });

    $api->group(['namespace' => 'App\Api\V1\Controllers\Admin'], function($api) {
        $dir = app_path().'/Api/V1/Controllers/Admin/' ;
        include $dir.'routes.php';;
    });

});
