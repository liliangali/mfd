<?php

class SizepartModel extends BaseModel
{
    var $table  = 'size_part';
    var $prikey = 'id';
    var $_name  = 'size_part';

    /* 关系列表 */
    var $_relation  = array(
        // 一个会员拥有多个收货地址
        'has_repair_part' => array(
            'model' => 'repairpart',
            'type' => HAS_ONE,
            'foreign_key' => 'partid',
            'refer_key'       => 'part_ecode',
            'dependent' => true
        ),
    );



}

