<?php
/**
 * 兑换券商品购物车类
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: ticket.gtype.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package normal.gtype.php
 */
class TicketGoods extends BaseGoods
{
	
    function __construct()
    {
        $param['_name']         = 'ticket';
        $param['_order_type']   = 'ticket';  //貌似没什么用，尼玛订单都是混合生成的！
        parent::__construct($param);
    }
    /**
     * 格式化POST数据
     *
     * @version 1.0.0 (Jan 8, 2015)
     * @author Ruesin
     */
    function _format_post($post = array()){
        $res = parent::_format_post($post);
        $res['btype']    = $this->_name;
        $res['spec']     = !empty($post['spec']) ? ksort($post['spec']) ? serialize($post['spec']) :'' : '';
        //$items    = isset($post['items']) ? trim($post['items']) : '';
        if(intval($post['measure']) == 1) $res['spec'] = "need_measure";  //针对固话款
        $res['card_ticket'] = intval($post['card_ticket_id']); 
        if(!$res['card_ticket'])return false;
        return $res;
    }
    /**
     * 商品加入购物车
     * 
     * @version 1.0.0 (2014-11-23)
     * @author Ruesin
     */
    function add($post)
    {
        $pData = $this->_product_info($post);
        
        if(!$pData) {
            $this->_error("product_have_not");
            return false;
        }
    	
    	$post['product_id'] = $pData['product_id'];
    	
    	$cartInfo = $this->_cart->get(array(
    	        'conditions' => "city_id = '".$post['city_id']."' AND card_ticket = '".$post['card_ticket']."' AND user_id = '".$post['user_id']."'"
    	));
    	//时效性/券关联商品 验证未做 
    	//..
    	//
    	
    	//一个券关联只能用一次
    	if(!empty($cartInfo)){
    	    $this->_error("card_ticket_had_use");
            return false;
    	}
    	
    	$stock = $this->_check_stock($post);
        if($stock < 0){
            $this->_error("goods_stock_error");
            return false;
        }
        
        $aSave = array(
                'params'       => $this->_generateParams($post),
                'goods_id'     => $post['goods_id'],
                'product_id'   => $post['product_id'],
                'user_id'      => $post['user_id'],
                'city_id'      => $post['city_id'],
                'type'         => $this->_name,
                'card_ticket'  => $post['card_ticket'],
                'quantity'     => $post['quantity'],
                'time'         => time(),
        );
        
        if($pData['goods_spec'] == 'need_measure') {
            $aSave['need_measure'] = 1;  //需要量体
        }
        
        $res = $this->_cart->add($aSave);
        if(!$res)
        {
            $this->_error("add_to_cart_error");
            return false;
        }

        return $res;
    	
    }
    
    /**
     * 获取购物车项小计
     *
     * @version 1.0.0 (Jan 8, 2015)
     * @author Ruesin
     */
    function _get_subtotal(&$item){
        $item['subtotal'] = 0;
        //$item['subtotal'] = $item['goods']['price'] * $item['quantity'];
    }
    
    /**
     * 删除定制商品购物车项
     *
     * 
     * @version 1.0.0 (2014-11-19)
     * @author Ruesin
     */
    function drop($post){
    
        $droped_rows = $this->_cart->drop("id='".$post['id']."' AND user_id = '".$post['user_id']."' ");
        
        if (!$droped_rows)
        {
            $this->_error("drop_error");
            return false;
        }
        
        return true;
    }

}
