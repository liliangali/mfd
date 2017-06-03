<?php

/* 面料数据模型 */
class SuitlistModel extends BaseModel
{
    var $table  = 'suit_list';
    var $prikey = 'id';
    var $_name  = 'suitlist';
	var $_obj_search='suit_name|套装名称';
	var $_obj_fields='id|ID,suit_name|名称,price|价格,image|图片';
	var $_obj_images = 'image';
    var $_relation = array(
        'belongs_to_design' => array(
            'model'             => 'designer',
            'type'              => HAS_ONE,
            'foreign_key'       => 'id',
            "refer_key"         => "design_id"
        ),
            //sin add 2015-05-14 16:56:09
            'has_one_suit_relation' => array(
                    'model'             => 'suitrelat',
                    'type'              => HAS_ONE,
                    'foreign_key'       => 'tz_id',
                    "refer_key"         => "id"
            ),
        
        //=====  add by liang.li  =====
        'has_suitlink' => array(
            'model' => 'suitlink',
            'type' => HAS_MANY,
            'foreign_key' => 'suit_id',
        ),
    );
    
   /*  function find($params = array()){
        if(isset($params["conditions"])){
            $params["conditions"] .= " AND theme != 11";
        }
        return parent::find($params);
    } */
}


?>