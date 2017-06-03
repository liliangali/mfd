<?php

/* 订单 order */
class OrderrefundModel extends BaseModel
{
    var $table  = 'order_refund';
    var $prikey = 'id';
    var $_name  = 'orderrefund';
 
 
    var $_relation = array(
        'belongs_to_order' => array(
            'model'             => 'order',
            'type'              => HAS_ONE,
            'foreign_key'       => 'order_id',
            'refer_key'         => 'order_id',
        ),
    );
}

