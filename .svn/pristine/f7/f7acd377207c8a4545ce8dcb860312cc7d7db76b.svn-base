<?php

/* 面料数据模型 */
class WorkSuitModel extends BaseModel
{
    var $table  = 'work_suit';
    var $prikey = 'id';
    var $_name  = 'worksuit';
    var $_prefix= 'diy_';
    var $_obj_search='suit_name|套装名称';
    var $_obj_fields='id|ID,suit_name|名称,price|价格,image|图片';
    var $_obj_images = 'image';
    var $_relation = array(

        //sin add 2015-05-14 16:56:09
        'has_one_suit_relation' => array(
            'model'             => 'suitrelat',
            'type'              => HAS_ONE,
            'foreign_key'       => 'tz_id',
            "refer_key"         => "id"
        ),
    );
}


?>