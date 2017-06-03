<?php

// 订单列表
$api->get('/mfd/order/all', 'Order\OrderController@getAllOrder')->middleware(['jwt.auth']);
$api->get('/mfd/order/statis', 'Order\OrderController@getStatisOrder')->middleware(['jwt.auth']);
