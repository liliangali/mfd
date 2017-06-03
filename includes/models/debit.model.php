<?php

/* 会员 member */
class DebitModel extends BaseModel
{
    var $table  = 'debit';
    var $prikey = 'id';
    var $_name  = 'debit';

    var $_relation = array(

        'has_member' => array(
            'model' => 'member',
            'type' => HAS_ONE,
            'foreign_key' => 'user_id',
            'refer_key'       => 'user_id',
            'dependent' => true
        ),

    );
}

?>