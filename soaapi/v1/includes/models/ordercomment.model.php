<?php

/* 订单 order */
class OrdercommentModel extends BaseModel
{
    var $table  = 'order_comment';
    var $alias  = 'oc';
    var $prikey = 'id';
    var $_name  = 'oc';
    var $_relation  = array(
        'belongs_to_user'  => array(
            'type'          => BELONGS_TO,
            'reverse'       => 'has_comment',
            'model'         => 'member',
        ),
    );
}

?>
