<?php
/**
 * 普通商品(成衣)购物车类
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: normal.gtype.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package normal.gtype.php
 */
class NormalGoods extends BaseGoods
{
    function __construct()
    {
        $param['_is_diy']       = false;
        $param['_name']         = 'normal';
        $param['_order_type']   = 'normal';
        parent::__construct($param);
    }
    /**
     * 普通商品（成衣）加入购物车
     * @param array $post post数据
     * @return bool
     * @access public
     * @see add
     * @version 1.0.0 (2014-11-14)
     * @author Ruesin
     */
    function add($post)
    {
        $pData = $this->_product_info($post);
        if(!$pData) return false;
        
        $post['product_id'] = $pData['product_id'];
        
        $cartInfo = $this->_cart->get(array(
                'conditions' => " city_id = '".$post['city_id']."' AND product_id = '".$post['product_id']."' AND user_id = '".$post['user_id']."'"
        ));
        
        //更新|追加
        if(!empty($cartInfo)){
            $post['quantity']  += $cartInfo['quantity'];
            $post['id']         = $cartInfo['id'];
            $res = $this->update($post);
            return $res;
        }

        $stock = $this->_check_stock($post);
        if($stock < 0){
            $this->_error("goods_stock_error");
        }
        
        $aSave = array(
                'params'     => $this->_generateParams($post),
                'goods_id'   => $post['goods_id'],
                'product_id' => $post['product_id'],
                'user_id'    => $post['user_id'],
                'city_id'    => $post['city_id'],
                'type'       => $this->_name,
                'quantity'   => $post['quantity'],
                'time'       => time(),
        );
        
//      dump($aSave);
        $res = $this->_cart->add($aSave);
        if(!$res)
        {
            $this->_error("add_to_cart_error");
        }
        return $res;
    }
    
}
