<?php
class CustomattributeModel extends BaseModel
{
    var $table  = 'custom_attribute';
    var $prikey = 'attr_id';
    var $alias  = 'attr';
    var $_name  = 'custom_attribute';
    var $_prefix = "diy_";
    var $temp; // 临时变量
    var $_relation = array(
        'belongs_to_type' => array(
            'model'             => 'customtype',
            'type'              => HAS_ONE,
            'foreign_key'       => 'type_id',
            "refer_key"         => "type_id"
        ),
    );
}


?>