<?php

class CustomModel extends BaseModel
{
    var $table  = 'custom';
    var $prikey = 'id';
    var $alias  = 'c';
    var $_name  = 'custom';
	var $_obj_search='name|基本款名称';
	var $_obj_fields='id|ID,name|名称,price|价格,source_img|大图';
	var $_obj_images = 'source_img';
    var $_prefix = "diy_";
    var $temp; // 临时变量
    var $_relation = array(
//         'belongs_to_design' => array(
//             'model'             => 'designer',
//             'type'              => HAS_ONE,
//             'foreign_key'       => 'id',
//             "refer_key"         => "design_id"
//         ),
    );
}


?>