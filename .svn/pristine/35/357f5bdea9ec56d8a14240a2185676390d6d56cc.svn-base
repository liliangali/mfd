<?php

class OrdersuitModel extends BaseModel
{
    var $table  = 'order_suit';
    var $prikey = 'id';
    var $_name  = 'ordersuit';
    
    var $_relation = array(
        // 一个订单商品只能属于一个订单
        'belongs_to_order' => array(
            'model'         => 'order',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'order_id',
            'reverse'       => 'has_ordersuit',
        ),
    );
    
}

