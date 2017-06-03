<?php

class BookrefundModel extends BaseModel
{
    var $table  = 'bookrefund';
    var $prikey = 'id';
    var $_name  = 'refund';
    
    var $_relation = array(
        'belongs_to_order' => array(
            'model'             => 'order',
            'type'              => HAS_ONE,
            'foreign_key'       => 'order_id',
            'refer_key'           => 'order_id',
        ),
    );
}

?>