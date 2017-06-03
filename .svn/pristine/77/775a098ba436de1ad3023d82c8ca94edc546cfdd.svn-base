<?php
class CouponModel extends BaseModel{
    var $table  = 'coupon';
    var $prikey = 'cpn_id';
    var $_name  = 'coupon';
    var $alias  = 'cpn';
    var $_obj_search  ='title|优惠券名称';
    var $_obj_fields='cpn_id|ID,cpn_name|名称,start_time|开始时间,end_time|结束时间,cpn_money|金额';
    
    var $_relation  = array(
        // 一种优惠券有多个优惠券编号
        'has_couponsn' => array(
            'model'         => 'couponsn',
            'type'          => HAS_ONE,
            'foreign_key'   => 'cpn_id',
            'dependent'     => true
        ),
        // 一种优惠券只能属于一个店铺
        'belong_to_store' => array(
            'model'         => 'store',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'store_id',
            'reverse'       => 'has_coupon',    
        ),
    );




    
}
