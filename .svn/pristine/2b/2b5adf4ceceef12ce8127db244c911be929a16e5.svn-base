<?php

/* 订单量体数据 */
class OrderfigureModel extends BaseModel
{
    var $table  = 'order_figure';
    var $prikey = 'id';
    var $_name  = 'order_figure';
    
    var $_relation = array(
    		// 一个用户只能被一个人邀请
    		'has_order' => array(
    				'model' => 'order',
    				'type' => HAS_ONE,
    				'foreign_key' => 'order_id',
    				'refer_key' => 'order_id',
    				'dependent' => true
    		),
    	);
   
    
}
