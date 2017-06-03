<?php
class OrderShipDelayModel extends BaseModel
{
    var $table  = 'order_ship_delay';
    var $prikey = 'id';
    var $_name  = 'order_ship_delay';
	
	
	/**
     * 获得当前订单是可以进行延期发货
     * @param $sn 订单sn
     * @version 1.0.0 (Jan 4, 2015)
     * @author Ruesin
     */
    function _order_ship_delay($order_id = ''){
		
        $return['code'] = 0;
        $return['msg'] = "";
		$count = $this->get(array(
			'conditions' => "order_id = $order_id",
			'fields'     => "count(id) as count",
			'index_key'  => '',
		));
		
        if ($count >= 2) {
            $return['msg'] = "超过限定操作次数";
        } else {
			$return['code'] = 1;
			$return['msg'] = "可进行延期操作";	
		}
		
		return $return;
    }
	
}