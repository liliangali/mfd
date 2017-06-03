<?php

/* 会员评论 */
class CommentsModel extends BaseModel
{
    var $table  = 'comments';
    var $prikey = 'id';
    var $_name  = 'comments';
    var $alias  = 'cmt';
    var $_relation  = array(
        'belongs_to_user'  => array(
            'type'          => BELONGS_TO,
            'reverse'       => 'has_comment',
            'model'         => 'member',
        ),
    );
}

?>