<?php

class CustomModel extends BaseModel
{
    var $table  = 'custom';
    var $prikey = 'id';
    var $alias  = 'c';
    var $_name  = 'custom';
    var $_prefix = "diy_";
    var $temp; // 临时变量
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