<?php

class CouponsnModel extends BaseModel{
    
    var $table  = 'coupon_sn';
    var $_name  = 'coupon_sn';
    var $prikey = 'coupon_sn';
    var $alias  = 'cpnsn';
    
    var $_relation  = array(
        'has_coupon' => array(
            'model'         => 'coupon',
            'type'          => HAS_ONE,
            'foreign_key'   => 'cpn_id',
            'refer_key'     => 'cpn_id',
            'dependent'     => true
        ),
    );
}

?>
