<?php

/* 套装属性模型*/
class SuitattrModel extends BaseModel
{
    var $table  = 'suit_attr';
    var $prikey = 'id';
    var $_name  = 'suitattr';

    var $_relation = array(
    		
        // 一个商品对应多个规格
        'has_goodsspec' => array(
            'model'         => 'goodsspec',
            'type'          => HAS_MANY,
            'foreign_key'   => 'goods_id',
            'dependent'     => true
        ),
        
    );
    
}


?>