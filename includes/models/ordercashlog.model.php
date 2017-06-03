<?php

/* 订单 order */
class OrdercashlogModel extends BaseModel
{
    var $table  = 'order_cash_log';
    var $prikey = 'id';
    var $_name  = 'ordercashlog';
    
    var $_relation = array(
        // 一条记录只能属于一个会员
        'belongs_to_member' => array(
            'model'             => 'member',
            'type'              => BELONGS_TO,
            'foreign_key'       => 'user_id',
            'reverse'           => 'has_order_cash',
        ),
    );
 
}

