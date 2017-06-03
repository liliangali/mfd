<?php

/* 促销规则 关联 商品 */
class Goods_prorelModel extends BaseModel
{
    var $table  = 'goods_promotion_relation';
    var $prikey = 'id';
    var $_name  = 'goods_promotion_relation';
	
    var $_relation = array(
    
        'has_custom' => array(
            'model'         => 'customs',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'c_id',
            'dependent'     => true,
            'reverse'       => 'belongs_to_links',
        ),
    
		"has_goods" => array(
			"model"   => "goods",
			"type"    => HAS_ONE,
			'foreign_key'   => 'goods_id',
            'refer_key'     => 'c_id'
		),
     );
}

?>