<?php

/* 申请角色日志 member */
class Member_singleModel extends BaseModel
{
    var $table  = 'member_single';
    var $prikey = 'id';
    var $_name  = 'member_single';
    // 面料和会员是多对多的关系（会员收藏面料） add by xiao5
    var $_relation = array(
        'be_collect' => array(
            'model'         => 'member',
            'type'          => HAS_AND_BELONGS_TO_MANY,
            'middle_table'  => 'collect',
            'foreign_key'   => 'item_id',
            'ext_limit'     => array('type' => 'single'),
            'reverse'       => 'collect_single',
        )
    );

}

?>