<?php

/* 订单 order */
class OrderModel extends BaseModel
{
    var $table  = 'order';
    var $alias  = 'order_alias';
    var $prikey = 'order_id';
    var $_name  = 'order';
    var $_relation  = array(
        // 一个订单有一个实物商品订单扩展
        'has_orderextm' => array(
            'model'         => 'orderextm',
            'type'          => HAS_ONE,
            'foreign_key'   => 'order_id',
            'dependent'     => true
        ),
        // 一个订单有多个订单商品
        'has_ordergoods' => array(
            'model'         => 'ordergoods',
            'type'          => HAS_MANY,
            'foreign_key'   => 'order_id',
            'dependent'     => true
        ),
        // 一个订单有多个订单日志
        'has_orderlog' => array(
            'model'         => 'orderlog',
            'type'          => HAS_MANY,
            'foreign_key'   => 'order_id',
            'dependent'     => true
        ),
        'belongs_to_store'  => array(
            'type'          => BELONGS_TO,
            'reverse'       => 'has_order',
            'model'         => 'store',
        ),
        'belongs_to_user'  => array(
            'type'          => BELONGS_TO,
            'reverse'       => 'has_order',
            'model'         => 'member',
        ),
        
            
        'has_orderfigure' => array(
            'model'         => 'orderfigure',
            'type'          => HAS_ONE,
            'foreign_key'   => 'order_id',
            'dependent'     => true
        ),
            
        
    );
    /**
     * 会员中心订单列表接口
     * @param int $user_id 会员id
     * @param int $limit 每页显示条数
     * @return array $return 订单数据
     * @access public
     * @see orderList
     * @version 1.0.0 (2014-12-2)
     * @author Ruesin
     */
    function orderList ($user_id = 0 , $limit = 10)
    {
        if($user_id == 0){
            return ;
        }
        $_ms = & ms();
        $data['user_id'] = $user_id;
        $data['limit']   = $limit;
       
        $return = $_ms->order->getOrderList($data);
        
        return $return;
    }
    
    /**
     * 根据订单sn和会员ID获取订单详情
     * @param string $order_sn 订单sn
     * @param int $user_id 用户id
     * @return array $return 订单详情
     * @access public
     * @see orderDetail
     * @version 1.0.0 (2014-12-2)
     * @author Ruesin
     */
    function orderDetail ($order_sn = '' , $user_id = 0)
    {
        if($order_sn == '' || $user_id == 0)return ;
        
        $_ms = & ms();
         
        $return = $_ms->order->getOrderDetail($order_sn,$user_id);
        
        return $return;
    }
    
    /**
     *    修改订单中商品的库存，可以是减少也可以是加回
     *
     *    @author    Garbin
     *    @param     string $action     [+:加回， -:减少]
     *    @param     int    $order_id   订单ID
     *    @return    bool
     */
    function change_stock($action, $order_id)
    {
        if (!in_array($action, array('+', '-')))
        {
            $this->_error('undefined_action');

            return false;
        }
        if (!$order_id)
        {
            $this->_error('no_such_order');

            return false;
        }

        /* 获取订单商品列表 */
        $model_ordergoods =& m('ordergoods');
        $order_goods = $model_ordergoods->find("order_id={$order_id}");
        if (empty($order_goods))
        {
            $this->_error('goods_empty');

            return false;
        }

        $model_goodsspec =& m('goodsspec');
        $model_goods =& m('goods');

        /* 依次改变库存 */
        foreach ($order_goods as $rec_id => $goods)
        {
            $model_goodsspec->edit($goods['spec_id'], "stock=stock {$action} {$goods['quantity']}");
            $model_goods->clear_cache($goods['goods_id']);
        }

        /* 操作成功 */
        return true;
    }
}

?>
