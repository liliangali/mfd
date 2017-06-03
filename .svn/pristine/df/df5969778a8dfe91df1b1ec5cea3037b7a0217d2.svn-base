<?php

/* 面料数据模型 */
class WorkSuitrelatModel extends BaseModel
{
    var $table  = 'suit_relation';
    var $prikey = 'id';
    var $_name  = 'suitrelat';
    var $_prefix= 'diy_';

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