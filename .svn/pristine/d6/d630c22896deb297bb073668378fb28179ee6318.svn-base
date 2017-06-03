<?php

/* 配送方式 shipping */
class ShippingareaModel extends BaseModel
{
    var $table  = 'shipping_area';
    var $prikey = 'area_id';
    var $_name  = 'shipping_area';
    var $_autov = array(
        'area_name' =>  array(
            'required'  => true,
            'filter'    => 'trim',
        ),
        'first_price'   =>  array(
            'required'  => true,

        ),
        'step_price'    =>  array(
        	'required'  => true,
        ),
    		
    	'areas'         =>  array(
    		'required'  => true,
    		'filter'    => 'serialize',
        	'type'      => 'array'
    	),
    		
    	'shipping_id'  => array(
    			'required'  => true,
    			'filter'    => 'intval',
    	),
    );
}

?>