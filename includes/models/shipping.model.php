<?php

/* 配送方式 shipping */
class ShippingModel extends BaseModel
{
    var $table  = 'shipping';
    var $prikey = 'shipping_id';
    var $_name  = 'shipping';
    var $_autov = array(
        'shipping_name' =>  array(
            'required'  => true,
            'filter'    => 'trim',
        ),
//         'first_weight'   =>  array(
//             'required'  => true,
//             'filter'    => 'intval',
//         ),
//         'step_weight'    =>  array(
//             'filter'    => 'intval'
//         ),
        'code'   =>  array(
            'filter'    => 'trim',
        	'required'  => true,
        ),
        'enabled'       =>  array(
            'filter'    => 'intval',
        ),
        'sort_order'    =>  array(
            'filter'    => 'intval'
        ),
    );

    var $_relation  =   array(
        // 一个配送方式对应多个配送地区
        'belongs_to_area' => array(
            'model'         => 'shippingarea',
            'type'          => HAS_ONE,
            'foreign_key'   => 'shipping_id',
        	'dependent'     => true
        ),
    );
}

?>