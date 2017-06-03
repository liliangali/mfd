<?php
/**
 *    银联支付Unionpay接口
 *
 *    @author Ruesin
 */
class UnionpayPayment extends BasePayment{

    public $display_name = '中国银联UnionPay';
    public $curname = 'CNY';
    public $ver = '1.0';
    public $platform = 'ispc';
    public $supportCurrency = array("CNY"=>"156");
    protected $fields = array();
    protected $callback_url;

    
    var $_gateway   =   'http://202.101.25.184/UpopWeb/api/Pay.action';  //https://unionpaysecure.com/api/Pay.action
    var $_code      =   'unionpay';
    
    public function __construct($payment_info = array()){
        $this->UnionpayPayment($payment_info);
    }
    function UnionpayPayment($payment_info){
        parent::__construct($payment_info);
        
    }
    /**
     * 创建付款单
     * 
     * @version 1.0.0 (Jan 9, 2015)
     * @author Ruesin
     */
    function createBill($order){
         
        $payment_sn = $this->get_payment_sn();
    
        $bill = array(
                'payment_sn' => $payment_sn,
                'amount'     => $order['order_amount'],
                'member_id'  => $order['user_id'],
                'account'    => $this->_config['unionpay_mer_id'],
                'ip'         => real_ip(),
                'start_time' => time(),
                'order_sn'   => $order['order_sn'],
                'pay_id'     => $order['payment_id'],
                'pay_code'   => $order['payment_code'],
                'pay_name'   => $order['payment_name'],
        );
         
        $this->out_trade = $payment_sn;   ///外部交易号
         
        $res = $this->_mod_bills->add($bill);
        if(!$res){
            return false;
        }
        
        $_ms = & ms();
        $res = $_ms->order->edit_order_by_params(array("order_id" => $order['order_id']),array("out_trade_sn" => $payment_sn));

    	if(!$res){
    		return false;
    	}
        return true;
    }
    
    /**
     * 获取支付表单
     * 
     * @version 1.0.0 (Jan 9, 2015)
     * @author Ruesin
     */
    public function get_payform($order_info,$type = "order"){
        //print_r($this->_config);exit; [unionpay_mer_id] => 802053256993126 [unionpay_mer_key] => 88888888 [unionpay_pay_fee] =>
        $merId = trim($this->_config['unionpay_mer_id']);//客户号
        $mer_key = trim($this->_config['unionpay_mer_key']);//密钥
    
        //组织给银联打过去要签名的数据 $args
        $args = array(
                "version"=>"1.0.0",//消息版本号
                "charset"=>"UTF-8",//字符编码
                "transType"=>'01',//交易类型
                "merAbbr"=>'青岛凯妙服饰股份有限公司分公司',//商户名称
                "merId"=>$merId,//商户代码
                "merCode"=>"",//商户类型
                "backEndUrl"=> $this->_create_notify_url($order_info['order_sn'], $type),//通知的url      ///=============
                "frontEndUrl"=> $this->_create_return_url($order_info['order_sn'], $type),//返回的url  ////=====
                "acqCode"=>"",//收单机构代码
                "orderTime"=>date('YmdHis', time()),//交易开始日期时间///=============
                "orderNumber"=>$this->out_trade,//订单号(这里写支付单号)
                "commodityName"=>"",//商品名称
                "commodityUrl"=>"",//商品的url
                "commodityUnitPrice"=>"",//商品单价
                "commodityQuantity"=>"",//商品数量
                "transferFee"=>"",//运输费用
                "commodityDiscount"=>"",//优惠信息
                "orderAmount"=>ceil($order_info['order_amount'] * 100),//交易金额
                "orderCurrency"=>156,//交易币种
                "customerName"=>"",//持卡人姓名
                "defaultPayType"=>"",//默认支付方式
                "defaultBankNumber"=>"",//默认银行编码
                "transTimeout"=>"",//交易超时时间
                "customerIp"=>$_SERVER['REMOTE_ADDR'],//持卡人IP
                "origQid"=>"",//交易流水号
                "merReserved"=>"",//商户保留域
        );
    
        //生成签名
        $args['signature']  = $this->sign($args, 'MD5',$mer_key); //签名
        $args['signMethod'] = 'MD5';   //签名方法
        return $this->_create_payform('POST', $args);
    }
    
    /**
     * 返回通知
     * 
     * @version 1.0.0 (Jan 9, 2015)
     * @author Ruesin
     */
    function verify_notify($order_info, $strict = false){
        if (empty($order_info))
        {
            $this->_error('order_info_empty');
            return false;
        }
        
        /* 初始化所需数据 */
        $notify =   $this->_get_notify();
        
        /* 验证来路是否可信 */
        if ($strict){
            /* 严格验证 */
//             $verify_result = $this->_query_notify($notify['notify_id']);
//             if(!$verify_result){
//                 /* 来路不可信 */
//                 $this->_error('notify_unauthentic');
//                 return false;
//             }
        }

        /* 验证通知是否可信 */
        $sign_result = $this->_verify_sign($notify);
        if (!$sign_result){
            /* 若本地签名与网关签名不一致，说明签名不可信 */
            $this->_error('sign_inconsistent');
            return;
        }
        /*----------通知验证结束----------*/
        
        /*----------本地验证开始----------*/
        /* 验证与本地信息是否匹配 */
        /* 这里不只是付款通知，有可能是发货通知，确认收货通知 */
        
        if ($order_info['out_trade_sn'] != $notify['orderNumber']){
            /* 通知中的订单与欲改变的订单不一致 */
            $this->_error('order_inconsistent');
            return false;
        }
        
        if ($order_info['order_amount'] != $notify['settleAmount']/100){
            /* 支付的金额与实际金额不一致 */
            $this->_error('price_inconsistent');
            return false;
        }
        //至此，说明通知是可信的，订单也是对应的，可信的
        
        $billdata = array(
                'status'    => $notify['respCode'],
                'end_time'	=> time(),
                'buyer_name' => $notify['buyer_email'],//中国银联没有返回用户名? -- ruesin
        );
        
        $this->updateBill($notify['orderNumber'], $billdata);
        
        /* 按通知结果返回相应的结果 */
        $order_status = ORDER_ACCEPTED;  //目前不知道怎么判断返回的rescode 就先做这一个吧 -- ruesin
        return array(
                'target'    =>  $order_status,
        );
        
    }
    
    /*
     签名方法 3个参数
     $params:组织给银联发过去的数据
     $sign_method:签名的加密方法
     $mer_key:签名必须要附上商户密钥
     */
    private function sign($params, $sign_method,$mer_key)
    {
        if (strtoupper($sign_method) == "MD5") {
            ksort($params);
            $sign_str = "";
            foreach ($params as $key => $val) {
                if (in_array($key, array("bank",))) {
                    continue;
                }
                $sign_str .= sprintf("%s=%s&", $key, $val);
            }
            $sign=$sign_str. $sign_method($mer_key);
            return $sign_method($sign);
        }else {
            $this->_error("Unknown sign_method set in quickpay_conf");
        }
    }
    /**
     *    查询通知是否有效
     *
     *    @author Ruesin
     */
    function _query_notify($notify_id)
    {
        $query_url = "http://notify.alipay.com/trade/notify_query.do?partner={$this->_config['alipay_partner']}&notify_id={$notify_id}";
    
        return (ecm_fopen($query_url, 60) === 'true');
    }
    
    /**
     *    验证签名是否可信
     *
     *    @author Ruesin
     */
    function _verify_sign($recv)
    {
        $merId = trim($this->_config['unionpay_mer_id']);//客户号
        $mer_key = trim($this->_config['unionpay_mer_key']);//密钥
        
        $sign=$recv['signature'];  //银联返回来的签名
        $sign_method=$recv['signMethod'];//银联返回来的签名方法
        //$arrs:银联返回来的数组
        $arrs=array(
                "version"=>$recv['version'],//消息版本号
                "charset"=>$recv['charset'],//字符编码
                "transType"=>$recv['transType'],//交易类型
                "respCode"=>$recv['respCode'],//响应码
                "respMsg"=>$recv['respMsg'],//响应信息
                "merAbbr"=>$recv['merAbbr'],//商户名称
                "merId"=>$recv['merId'],//商户代码
                "orderNumber"=>$recv['orderNumber'],//订单号
                "traceNumber"=>$recv['traceNumber'],//系统跟踪号
                "traceTime"=>$recv['traceTime'],//系统跟踪时间
                "qid"=>$recv['qid'],//交易流水号
                "orderAmount"=>$recv['orderAmount'],//交易金额
                "orderCurrency"=>$recv['orderCurrency'],//交易币种
                "respTime"=>$recv['respTime'],//交易完成时间
                "settleCurrency"=>$recv['settleCurrency'],//清算币种
                "settleDate"=>$recv['settleDate'],//清算日期
                "settleAmount"=>$recv['settleAmount'],//清算金额
                "exchangeDate"=>$recv['exchangeDate'],//兑换日期
                "exchangeRate"=>$recv['exchangeRate'],//清算汇率
                "cupReserved"=>$recv['cupReserved'],//系统保留域
        );
        //生成签名
        $local_sign = $this->sign($arrs, $sign_method,$mer_key);
        if ($sign==$local_sign && $recv['respCode']==00) {
            return true;
        }else{
            return false;
        }
    }


}
