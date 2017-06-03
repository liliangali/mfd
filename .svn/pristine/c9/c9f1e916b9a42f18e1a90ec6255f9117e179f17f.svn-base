<?php

/**
 * 评论(详情页)
 *  @author  yusw (2015-05-09)
 */
class detail_commentModel extends BaseModel
{
    var $table  = 'detail_comments';
    var $prikey = 'id';
    var $_name  = 'detailcomments';
    var $_relation = array(
    		// 评论关联用户
    		'belongs_to_user' => array(
    				'model'         => 'member',
    				'type'          => HAS_ONE,
    				'foreign_key'   => 'user_id',
    				'refer'       => 'member_id',
    		),
    );

    var $_autov = array(
    );
}


?>