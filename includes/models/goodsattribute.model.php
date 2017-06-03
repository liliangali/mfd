<?php
class GoodsattributeModel extends BaseModel
{
    var $table  = 'goods_attribute';
    var $prikey = 'attr_id';
    var $alias  = 'attr';
    var $_name  = 'goods_attribute';
    var $_prefix = "cf_";
    var $temp; // 临时变量
    var $_relation = array(
        'belongs_to_type' => array(
            'model'             => 'goodstype',
            'type'              => HAS_ONE,
            'foreign_key'       => 'type_id',
            "refer_key"         => "type_id"
        ),
    );
}


?>