<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |

     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |Access-Control-Allow-Origin
     */
    'supportsCredentials' => false,
    'allowedOrigins' => ['*'],//'http://mfd.p.day900.com','http://120.27.47.135:8090'
    'allowedHeaders' => ['Content-Type', '*'],
    'allowedMethods' => ['*'], // ex: ['GET', 'POST', 'PUT',  'DELETE']
    'exposedHeaders' => [],
    'maxAge' => 0,
];

