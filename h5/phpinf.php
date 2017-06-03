<?php

$user_agent = $_SERVER['HTTP_USER_AGENT'];

if (stripos($_SERVER['HTTP_USER_AGENT'], "Huawei")!==false) {
        echo $brand = '华为';
}