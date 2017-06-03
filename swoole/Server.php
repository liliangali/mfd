<?php
/**
 * Created by PhpStorm.
 * User: tandi
 * Date: 17/4/19
 * Time: 上午11:53
 */

$http = new swoole_http_server("127.0.0.1", 9501);
$http->on('request', function ($request, $response) {
    $response->end("<h1>Hello Swoole. #".$request->header['host']."</h1>");
});
$http->start();


//$http = new swoole_http_server("127.0.0.1", 9501);
//$http->on('request',function ($request,$response){
//    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
//});
//$http->start();