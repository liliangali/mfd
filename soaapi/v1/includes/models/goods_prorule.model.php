<?php

/* 商品促销 */
class Goods_proruleModel extends BaseModel
{
    var $table  = 'goods_promotion_rules';
    var $prikey = 'id';
    var $_name  = 'goods_promotion_rules';
    var $_relation = array(
    
    		'has_prorel' => array(
    				'model'         => 'goods_promotion_relation',
    				'type'          => HAS_ONE,
    				'foreign_key'   => 'd_id',
    				'refer_key'     => 'id'
    				
    		),
    
    		"has_prolink" => array(
    				"model"   => "goods_promotion_link",
    				"type"    => HAS_ONE,
    				'foreign_key'   => 'rules_id',
    				'refer_key'     => 'id'
    		),
    );

}

?>