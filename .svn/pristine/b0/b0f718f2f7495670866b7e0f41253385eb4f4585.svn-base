<?php

class SuitlinkModel extends BaseModel
{
    var $table  = 'suit_link';
    var $prikey = 'id';
    var $_name  = 'suit_link';
    var $_relation = array(
    		 'belongs_to_suit' => array(
    		'model'             => 'suitlist',
    		'type'              => HAS_ONE,
    		'foreign_key'       => 'id',
    		"refer_key"         => "suit_id"
    ),
    );
}


?>