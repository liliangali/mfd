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
    'allowedOrigins' => ['http://dev.mfd.com','http://localhost:8090','http://localhost:8091','http://mfd.p.day900.com','http://120.27.47.135:8090','http://www.lovetd.cn'],//'http://mfd.p.day900.com','http://120.27.47.135:8090'
    'allowedHeaders' => ['Content-Type', '*'],
    'allowedMethods' => ['*'], // ex: ['GET', 'POST', 'PUT',  'DELETE']
    'exposedHeaders' => [],
    'maxAge' => 0,
];