<?php

/* 面料数据模型 */
class SuitlinkModel extends BaseModel
{
    var $table  = 'suit_link';
    var $prikey = 'id';
    var $_name  = 'suitlink';
     
    var $_relation = array(
        'belongs_to_suit' => array(
            'model'             => 'suitlist',
            'type'              => BELONGS_TO,
            'foreign_key'       => 'suit_id',
            'reverse'           => 'has_suitlink',
        ),
    );
}


?>