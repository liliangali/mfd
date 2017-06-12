<?php

/*
 * Auth Controller Routes
 *
 */


$api->post('/mfd/login', 'Auth\AuthController@authenticate');
