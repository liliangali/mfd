<?php
/**
 *    新版银联支付Upop接口
 *
 *    @author 小五
 */
class UpopPayment extends BasePayment
{
    private static $registeredScripts = false;
    public $secureUtil;

    private $unionMerId = '898481159990002';    //商户号
    
    /**
     * Calls the {@link registerScripts()} method.
     */
    
    public function __construct($payment_info = array()){
        $this->UpopPayment($payment_info);
    }
    function UpopPayment($payment_info){
        $this->registerScripts();
        $this->secureUtil = new SecureUtil();
        parent::__construct($payment_info);
    
    }

    
    
    public function get_payform($attr,$type="order"){
        $log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
        $log->LogInfo ( "============处理前台请求开始===============" );

//        echo '<pre>';print_r($this->secureUtil->getSignCertId ($type));exit;

//        print_exit($this->secureUtil->getSignCertId ($type));
        // 初始化日志
        $params = array(
            'version' => '5.0.0',				//版本号
            'encoding' => 'utf-8',				//编码方式
            'certId' => $this->secureUtil->getSignCertId ($type),			//证书ID
//            'certId' => 70399382371,			//证书ID
            'txnType' => '01',				//交易类型
            'txnSubType' => '01',				//交易子类
            'bizType' => '000201',				//业务类型
            'backUrl' =>  $this->_create_notify_url($attr['order_sn'], $type),//前台通知地址
            'frontUrl' => $this->_create_return_url($attr['order_sn'], $type),//返回的url
            'signMethod' => '01',		//签名方法
            'channelType' => '07',		//渠道类型，07-PC，08-手机
            'accessType' => '0',		//接入类型
            'merId' => $this->unionMerId,		        //商户代码，请改自己的测试商户号
            'orderId' => $this->out_trade,//订单号(这里写支付单号)
            'txnTime' => date('YmdHis'),	//订单发送时间
            'txnAmt' => ceil($attr['order_amount'] * 100),//交易金额
            'currencyCode' => '156',	//交易币种
            'defaultPayType' => '0001',	//默认支付方式
            //'orderDesc' => '订单描述',  //订单描述，网关支付和wap支付暂时不起作用
            'reqReserved' =>' 透传信息', //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
        );

//        echo '<pre>';print_r($params);exit;
        
//        exit;
        // 签名

        $this->secureUtil->sign ( $params ,$type);


        // 前台请求地址
        $front_uri = SDK_FRONT_TRANS_URL;
        $log->LogInfo ( "前台请求地址为>" . $front_uri );
        // 构造 自动提交的表单
        $html_form = create_html ( $params, $front_uri );

        $log->LogInfo ( "-------前台交易自动提交表单>--begin----" );
        $log->LogInfo ( $html_form );
        $log->LogInfo ( "-------前台交易自动提交表单>--end-------" );
        $log->LogInfo ( "============处理前台请求 结束===========" );
        echo $html_form;
    }

    
    /**
     * 返回通知
     *
     * @version 1.0.0 (Jan 9, 2015)
     * @author Ruesin
     */
    public function verify_notify($order_info, $strict = false){
        $log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
        $log->LogInfo ( "============处理后台请求开始===============" );
        if (empty($order_info))
        {
            $this->_error('order_info_empty');
            return false;
        }
        $log->LogInfo ( "orderinfo>" . json_encode($order_info) );
        /* 返回模拟数据 */
//         $_POST['accNo'] = '6216***********0018';
//         $_POST['accessType'] = '0';
//         $_POST['bizType'] = '000201';
//         $_POST['certId'] = '68759585097';
//         $_POST['currencyCode'] = '156';
//         $_POST['encoding'] = 'utf-8';
//         $_POST['merId'] = '777290058134758';
//         $_POST['orderId'] = '14694141414878';
//         $_POST['queryId'] = '201607251035417837448';
//         $_POST['reqReserved'] = '﻿﻿﻿﻿fail';
//         $_POST['respCode'] = '00';
//         $_POST['respMsg'] = 'success';
//         $_POST['settleAmt'] = '64';
//         $_POST['settleCurrencyCode'] = '156';
//         $_POST['settleDate'] = '0724';
//         $_POST['signMethod'] = '01';
//         $_POST['signature'] = 'a9PRhcrlVmPkeK27W1PuqTupXbkyBc+tnbRTx8enfjZixY/6SVXTAzjVbA6h/VjRlW65AYcUO3V9AlFuWFKj/D2OwkYN+5bzjmqt47+o0ghdlaBrF5Ym0hgDaS/qBrK932nKbWj1GvKuVgE/tpYns0oluboJusivBcf7O1izkI4kv/Gd1pcC8UPGWqO9N88KW8SebkaeTSsFErbPrnNTa5O0QFP0L5ZNuoJ1TLU+xVOYWOP7SmwSShwaJlhSdLU4YlgbyYuJSxlT7dEgAQlkkyf7l5ttrkQfIT28skd+cbiSOn6QLBIiLDrmhfG6mMPOMZPUqE6cdSBxTe+PXNmj7w==';
//         $_POST['traceNo'] = '783744';
//         $_POST['traceTime'] = '0725103541';
//         $_POST['txnAmt'] = '64';
//         $_POST['txnSubType'] = '01';
//         $_POST['txnTime'] = '20160725103541';
//         $_POST['txnType'] = '01';
//         $_POST['version'] = '5.0.0';
  
        /* 初始化所需数据 */
        $notify =   $this->_get_notify();
    
        $log->LogInfo ( "notify>" . json_encode($notify) );
       /* 检查商户账号是否一致 */
        if ($this->unionMerId != $notify['merId'])
        {
            $log->LogInfo ( "error01>'支付失败，请重试！" );
            $this->_error('支付失败，请重试！');
            return false;
        }
       
        /* 检查支付的金额是否相符 */
        if ($order_info['order_amount'] != $notify['settleAmt']/100)
        { 
            $log->LogInfo ( "error02>'支付失败，支付金额与订单金额不符！" );
            $this->_error('支付失败，支付金额与订单金额不符！');
            return false;
        }

        if ($notify['respCode'] != '00')
        {
            $log->LogInfo ( "error03>'支付失败，请重试！错误信息：".$notify['respMsg'] );
            $this->_error('支付失败，请重试！错误信息：'.$notify['respMsg']);
            return false;
        }
     /*  测试环境先不验证签名 */
//         if($this->secureUtil->verify($notify) && $notify['respCode'] == '00')
        if($notify['respCode'] == '00')
        {
            
            $billdata = array(
                'status'    => $notify['respCode'],
                'end_time'	=> time(),
                'buyer_name' => $notify['accNo'],
            );
            
            $this->updateBill($notify['orderId'], $billdata);
            
            $order_status = ORDER_ACCEPTED;   //已付款
            $log->LogInfo ( "订单状态处理成功");
            $log->LogInfo ( "============处理后台请求 结束===========" );
            
            return array(
                'target'    =>  $order_status,
                'user_id'   => $order_info['user_id'],
            );
            
            	
        }else{
            $log->LogInfo ( "error04>'支付失败，请重试！错误信息：".$notify['respMsg'] );
            $log->LogInfo ( "============处理后台请求 结束===========" );
            $this->_error('支付失败，请重试！错误信息：'.$notify['respMsg']);
            return false;
            	
        }
    
    }
    
    public function searchOrder($attr,$type="bank"){
        $log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
        die('dddd');
        $log->LogInfo ( "============处理前台请求开始===============" );
        // 初始化日志
        if ($type == "bank") {
            $merId = '898110260110001';
        } else {
            $merId = '898110260110002';
        }
        $attr['txnTime'] = date("YmdHis",strtotime($attr['trade_time']));
        $params = array(
            'version' => '5.0.0',		//版本号
            'encoding' => 'utf-8',		//编码方式
            'certId' => $this->secureUtil->getSignCertId ($type),	//证书ID
            'signMethod' => '01',		//签名方法
            'txnType' => '00',		//交易类型
            'txnSubType' => '00',		//交易子类
            'bizType' => '000000',		//业务类型
            'accessType' => '0',		//接入类型
            'channelType' => '07',		//渠道类型
            'orderId' => $attr['trade_code'],	//请修改被查询的交易的订单号
            'merId' => $merId,	//商户代码，请修改为自己的商户号
            'txnTime' => $attr['txnTime'],	//请修改被查询的交易的订单发送时间				//业务类型
        );

//        print_r($params);
//        exit;
        // 签名

        $this->secureUtil->sign ( $params ,$type);


        // 前台请求地址
        $front_uri = SDK_SINGLE_QUERY_URL;
        $log->LogInfo ( "前台请求地址为>" . $front_uri );
        // 构造 自动提交的表单
        $html_form = create_html ( $params, $front_uri );
        $api_client  = new FApiClient();
        $res = $api_client->unionPaySend($front_uri,$params,"post");
        $log->LogInfo ( "-------前台交易自动提交表单>--begin----" );
        $log->LogInfo ( $html_form );
        $log->LogInfo ( "-------前台交易自动提交表单>--end-------" );
        $log->LogInfo ( "============处理前台请求 结束===========" );
        //echo $html_form;
        return $res;

    }
    /**
     * Registers swiftMailer autoloader and includes the required files
     */
    public function registerScripts() {
        $cf = '';
        if ($_SERVER['HTTP_HOST'] == 'mfd.p.day900.com'){
            $cf = 'formal.';
        }
        $cf = 'formal.';
        if (self::$registeredScripts) return;
        self::$registeredScripts = true;
        require dirname(__FILE__).'/func/common.php';
        require dirname(__FILE__).'/func/SDKConfig.'.$cf.'php';
        require dirname(__FILE__).'/func/secureUtil.php';

    }
}