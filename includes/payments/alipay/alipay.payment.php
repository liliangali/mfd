<?php

/**
 *    支付宝支付方式插件
 *
 *    @author    Garbin
 *    @usage    none
 */

class AlipayPayment extends BasePayment
{
    /* 支付宝网关 */ 
    var $_gateway   =   'https://www.alipay.com/cooperate/gateway.do';
    var $_code      =   'alipay';

   // http://local.rc.com/index.php/paynotify-1417043678.html?buyer_email=byhere%40126.com&buyer_id=2088202412480494&exterface=create_direct_pay_by_user&is_success=T&notify_id=RqPnCoPT3K9%252Fvwbh3InR9wvu1V0t4pjqQjboBkCjvBH0IrRH4rJDrsdCc%252BPFEn4alpoA&notify_time=2014-07-24+09%3A23%3A14&notify_type=trade_status_sync&out_trade_no=14061649607539&payment_type=1&seller_email=15698150123%40163.com&seller_id=2088301365987350&subject=rctailor.com+%E8%AE%A2%E5%8D%95%E5%8F%B7%3A1417043678&total_fee=0.10&trade_no=2014072412349749&trade_status=TRADE_SUCCESS&sign=2855f8e9dd6ed2e6fe5a1300bc3c6724&sign_type=MD5
    /**
     *    获取支付表单
     *
     *    @author    Garbin
     *    @param     array $order_info  待支付的订单信息，必须包含总费用及唯一外部交易号
     *    @return    array
     */
    function get_payform($order_info, $type ="order")
    {
        $service = $this->_config['alipay_service'];
        $agent = 'C4335319945672464113'; 

        $params = array(

            /* 基本信息 */
            'agent'             => $agent,
            'service'           => $service,
            'partner'           => $this->_config['alipay_partner'],
            '_input_charset'    => CHARSET,
            'notify_url'        => $this->_create_notify_url($order_info['order_sn'], $type),
            'return_url'        => $this->_create_return_url($order_info['order_sn'], $type),

            /* 业务参数 */
            'subject'           => $this->_get_subject($order_info),
            //订单ID由不属签名验证的一部分，所以有可能被客户自行修改，所以在接收网关通知时要验证指定的订单ID的外部交易号是否与网关传过来的一致
            'out_trade_no'      => $this->_get_trade_sn(),
            'price'             => $order_info['final_amount'],   //应付总价
            'quantity'          => 1,
            'payment_type'      => 1,

            /* 物流参数 */
            'logistics_type'    => 'EXPRESS',
            'logistics_fee'     => 0,
            'logistics_payment' => 'BUYER_PAY_AFTER_RECEIVE',

            /* 买卖双方信息 */
            'seller_email'      => $this->_config['alipay_account']
        );

        $params['sign']         =   $this->_get_sign($params);
        $params['sign_type']    =   'MD5';

        return $this->_create_payform('GET', $params);
    }

    /**
     *    返回通知结果
     */
    function verify_notify($order_info, $strict = false)
    {
        if (empty($order_info))
        {
            $this->_error('order_info_empty');
            return false;
        }
        //a:22:{s:8:"discount";s:4:"0.00";s:12:"payment_type";s:1:"1";s:7:"subject";s:25:"麦富迪订单:201510220510";s:8:"trade_no";s:28:"2015102221001004150002636846";s:11:"buyer_email";s:15:"lgx_1989@qq.com";s:10:"gmt_create";s:19:"2015-10-22 15:01:42";s:11:"notify_type";s:17:"trade_status_sync";s:8:"quantity";s:1:"1";s:12:"out_trade_no";s:14:"14454972965572";s:9:"seller_id";s:16:"2088611206542452";s:11:"notify_time";s:19:"2015-10-22 15:01:47";s:12:"trade_status";s:13:"TRADE_SUCCESS";s:19:"is_total_fee_adjust";s:1:"N";s:9:"total_fee";s:4:"0.01";s:11:"gmt_payment";s:19:"2015-10-22 15:01:47";s:12:"seller_email";s:20:"m18806390183@163.com";s:5:"price";s:4:"0.01";s:8:"buyer_id";s:16:"2088602051732150";s:9:"notify_id";s:34:"a1fc8ad6b1a2b63a9e2f07c926cb909h5o";s:10:"use_coupon";s:1:"N";s:9:"sign_type";s:3:"MD5";s:4:"sign";s:32:"d5322395318a3a56412f01f6c82b2ac3";}
        /* 初始化所需数据 */
        $notify =   $this->_get_notify();
//         $notify = array
//         (
//                 'discount' => 0.00,
//                 'payment_type' => 1,
//                 'subject' => '麦富迪订单:201510220510',
//                 'trade_no' => '2015102221001004150002636846',
//                 'buyer_email' => 'lgx_1989@qq.com',
//                 'gmt_create' => '2015-10-22 15:01:42',
//                 'notify_type' => 'trade_status_sync',
//                 'quantity' => 1,
//                 'out_trade_no' => 14454972965572,
//                 'seller_id' => 2088611206542452,
//                 'notify_time' => '2015-10-22 15:01:47',
//                 'trade_status' => 'TRADE_SUCCESS',
//                 'is_total_fee_adjust' => 'N',
//                 'total_fee' => 0.01,
//                 'gmt_payment' => '2015-10-22 15:01:47',
//                 'seller_email' => 'm18806390183@163.com',
//                 'price' => 0.01,
//                 'buyer_id' => '2088602051732150',
//                 'notify_id' => 'a1fc8ad6b1a2b63a9e2f07c926cb909h5o',
//                 'use_coupon' => 'N',
//                 'sign_type' => 'MD5',
//                 'sign' => 'd5322395318a3a56412f01f6c82b2ac3',
//         );
        
         
        /* 验证来路是否可信 */
        if ($strict)
        {
            /* 严格验证 */
            $verify_result = $this->_query_notify($notify['notify_id']);

            if(!$verify_result)
            {
                /* 来路不可信 */
                $this->_error('notify_unauthentic');
                return false;
            }
        }

        /* 验证通知是否可信 */
        $sign_result = $this->_verify_sign($notify);

        if (!$sign_result)
        {
            /* 若本地签名与网关签名不一致，说明签名不可信 */
            $this->_error('sign_inconsistent');
            return;
        }
        
        /*----------通知验证结束----------*/

        /*----------本地验证开始----------*/
        /* 验证与本地信息是否匹配 */
        /* 这里不只是付款通知，有可能是发货通知，确认收货通知 */
        if ($order_info['out_trade_sn'] != $notify['out_trade_no']&& !$order_info['out_trade_sns'][$notify['out_trade_no']])
        {
            $this->_error('order_inconsistent');
            return false;
        }

        if ($order_info['final_amount'] != $notify['total_fee'])
        {
            $this->_error('price_inconsistent');
            return false;
        }

		$billdata = array(
    			'status'     => $notify['trade_status'],
    			'end_time'	 => gmtime(),
				'buyer_name' => $notify['buyer_email'],
    		);

        $this->updateBill($notify['out_trade_no'], $billdata);
        /* 按通知结果返回相应的结果 */
        switch ($notify['trade_status'])
        {
            case 'WAIT_SELLER_SEND_GOODS':      //买家已付款，等待卖家发货
                $order_status = ORDER_ACCEPTED;   //已付款
            break;

            case 'TRADE_SUCCESS':  //交易成功  支付成功
		        $order_status = ORDER_ACCEPTED;   //已付款
            	break;

            case 'WAIT_BUYER_CONFIRM_GOODS':    //卖家已发货，等待买家确认
                $order_status = ORDER_SHIPPED;
            break;

            case 'TRADE_FINISHED':              //交易结束 完成
                if ($order_info['status'] == ORDER_PENDING)
                {
                    /* 如果是等待付款中，则说明是即时到账交易，这时将状态改为已付款 */
			        $order_status = ORDER_ACCEPTED;   //已付款
                }
                else
                {
                    /* 说明是第三方担保交易，交易结束 */
                    $order_status = ORDER_FINISHED;
                } 
            break;
            case 'TRADE_CLOSED':                //交易关闭
                $order_status = ORDER_CANCLED;
            break;

            default:
                $this->_error('undefined_status');
                return false;
            break;
        }

        switch ($notify['refund_status'])
        {
            case 'REFUND_SUCCESS':              //退款成功，取消订单
                $order_status = ORDER_CANCLED;
            break;
        }

        return array(
            'target'    =>  $order_status,
        );
    }

    /**
     *    查询通知是否有效
     *
     *    @author    Garbin
     *    @param     string $notify_id
     *    @return    string
     */
    function _query_notify($notify_id)
    {
        $query_url = "http://notify.alipay.com/trade/notify_query.do?partner={$this->_config['alipay_partner']}&notify_id={$notify_id}";

        return (ecm_fopen($query_url, 60) === 'true');
    }

    /**
     *    获取签名字符串
     *
     *    @author    Garbin
     *    @param     array $params
     *    @return    string
     */
    function _get_sign($params)
    {
        /* 去除不参与签名的数据 */
        unset($params['sign'], $params['sign_type'], $params['order_id'], $params['app'], $params['act']);

        /* 排序 */
        ksort($params);
        reset($params);

        $sign  = '';
        foreach ($params AS $key => $value)
        {
            $sign  .= "{$key}={$value}&";
        }

        return md5(substr($sign, 0, -1) . $this->_config['alipay_key']);
    }

    /**
     *    验证签名是否可信
     *
     *    @author    Garbin
     *    @param     array $notify
     *    @return    bool
     */
    function _verify_sign($notify)
    {
        $local_sign = $this->_get_sign($notify);

        return ($local_sign == $notify['sign']);
    }
}

?>