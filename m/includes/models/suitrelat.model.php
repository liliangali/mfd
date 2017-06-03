<?php

/* 面料数据模型 */
class SuitrelatModel extends BaseModel
{
    var $table  = 'suit_relation';
    var $prikey = 'id';
    var $_name  = 'suitrelat';

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