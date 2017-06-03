<?php

/* 会员 member */
class ApplyModel extends BaseModel
{
    var $table  = 'apply';
    var $prikey = 'id';
    var $_name  = 'apply';

    var $_relation = array(
    		// 一篇文章只能属于一个店铺
    		'belongs_to_user' => array(
    				'model'             => 'member',
    				'type'              => BELONGS_TO,
    				'foreign_key'       => 'user_id',
    				'reverse'           => 'has_apply',
    		),

    );
}

?>