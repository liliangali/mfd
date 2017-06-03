<?php

class SpecialquestionModel extends BaseModel
{
    var $table  = 'special_question';
    var $prikey = 'id';
    var $_name  = 'special_question';

    /* 关系列表 */
    var $_relation  = array(
        // 一个会员拥有多个收货地址
        'has_solution' => array(
            'model' => 'specialsolution',
            'type' => HAS_ONE,
            'foreign_key' => 'solution_id',
            'refer_key'       => 'id',
            'dependent' => true
        ),
    );


}

