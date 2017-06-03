<?php

/* 订单 order */
class OrdercashlogModel extends BaseModel
{
    var $table  = 'order_cash_log';
    var $prikey = 'id';
    var $_name  = 'ordercashlog';
	
	var $_relation  = array(
        // 一个订单日志只能属于一个订单
        'belongs_to_user' => array(
            'type'    => BELONGS_TO,
            'reverse' => 'has_ordercashlog',
            'model'   => 'member' 
        ),
    );
	
 
}

