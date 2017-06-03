<?php

/* 面料数据模型 */
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
        'belongs_to_links' => array(
            'model'         => 'goodsspec',
            'type'          => HAS_MANY,
            'foreign_key'   => 'goods_id',
            'dependent'     => true
        ),
        
        
        // 一个  样衣多个中间表的数据
        'has_link' => array(
            'model' => 'custom',
            'type' => HAS_MANY,
            'foreign_key' => 'c_id',
            'dependent' => true
        ),
        
    );
}


?>