<?php
/**
 * 新版DIY订单操作类
 * 
 * @author Ruesin <ruesin@163.com>
 * @version $Id: orders.app.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2015 redcollar
 */
class OrdersApp extends ShoppingbaseApp
{
    function __construct()
    {
        parent::__construct();
        $this->OrdersApp();
    }
    function OrdersApp(){
        
    }
    
    function create(){
        
        $otype = isset($_POST['type']) ? trim($_POST['type']) : 'normal';
        
        $_order = $this->_order_info();
  
    	$aCart = $this->_total($_order);
        
        if(!$aCart['object']){
            $this->json_error('购物车没有商品');
            return;
        }
        
        $order_type =& ots($otype);
        
        /* 事务开始 */
        $transaction = $this->_ms->order->begin();
        
        $oInfo = $order_type->submit(array(
                '_order' => $_order,
                '_cart'  => $aCart,
        ));
        
        if (!$oInfo)
        {
            /* 事务回滚 */
            $this->_ms->order->rollback();
            $this->json_error($order_type->get_error());
            return;
        }
         
        /* 提交 */
        $this->_ms->order->commit($transaction);
         
        // 订单生成成功清理购物车
        //$this->_clear();
        
        $this->json_result($oInfo);
    }
    
    /**
     * 清理购物车数据
     * 
     * @author Ruesin
     */
    function _clear()
    {
        $this->_mod_cart->drop(" user_id = '{$this->_user_id}'");
        unset($_SESSION["_order"]);
        unset($_SESSION["_cart"]);
    }
    
    
}
