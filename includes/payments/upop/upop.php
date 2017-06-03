<?php
$config =
array(//银联支付设置
    //测试环境参数
    /*'front_trans_Url' => 'https://101.231.204.80:5000/gateway/api/frontTransReq.do
     ', //前台交易请求地址
'signCertPath' => dirname(__FILE__).'\upop\700000000000001.pfx', //签名证书路径
'signCertPwd' => $payment['upop_security_key'], //签名证书密码
'verifyCertPath' => dirname(__FILE__).'\upop\UpopRsaCert.cer', //验签证书路径
'merId' => $payment['upop_account'] //商户号
*/

    //正式环境参数
    'front_trans_Url' => 'https://gateway.95516.com/gateway/api/frontTransReq.do', //前台交易请求地址
    'signCertPath' => dirname(__FILE__).'\upop\700000000000001.pfx', //签名证书路径
    'signCertPwd' => $payment['upop_security_key'], //签名证书密码
    'verifyCertPath' => dirname(__FILE__).'\upop\verify_sign_acp.cer', //验签证书路径
    'merId' => $payment['upop_account'], //商户代码
);
$unionPay->params = array(
    'version' => '5.0.0', //版本号
    'encoding' => 'utf-8', //编码方式
    'certId' => $unionPay->getSignCertId(), //证书ID
    'signature' => '', //签名
    'signMethod' => '01', //签名方式
    'txnType' => '01', //交易类型
    'txnSubType' => '01', //交易子类
    'bizType' => '000201', //产品类型
    'channelType' => '07',//渠道类型 07:PC端   08：移动端
    'frontUrl' => site_url().'?c=respond&code=upop', //前台通知地址
    'backUrl' => site_url().'?c=respond&code=upop', //后台通知地址
    //'frontFailUrl' => Url::toRoute(['payment/unionpayfail'], true), //失败交易前台跳转地址
    'accessType' => '0', //接入类型
    'merId' => $unionPay->config['merId'], //商户代码
    'orderId' => $order['order_sn'], //商户订单号
    'txnTime' => date('YmdHis'), //订单发送时间
    'txnAmt' => $order['total_order_price'] * 100, //交易金额，单位分
    'currencyCode' => '156', //交易币种
    'reqReserved' => '透传信息',
    'defaultPayType' => '0001',
);
$html = $unionPay->createPostForm();
return $html;


/**
 * 响应操作
 */
function respond()
{
    	
    include_once('upop/union.php');
    $unionPay = new UnionPay();
    $CI = get_instance();
    $CI->load->model('payment_model');
    $payment = $CI->payment_model->get_payment('upop');
    	
    if (!empty($_POST))
    {
        foreach($_POST as $key => $data)
        {
            $_GET[$key] = $data;
        }
    }else{

        ShowMsg('支付出错，请重试！若支付成功，请联系管理员！',site_url('customer/order'));
        exit();
    }

    $unionPay->config =
    array(//银联支付设置
        //测试环境参数
        /*
         'front_trans_Url' => 'https://101.231.204.80:5000/gateway/api/frontTransReq.do', //前台交易请求地址
    'signCertPath' => dirname(__FILE__).'\upop\700000000000001.pfx', //签名证书路径
    'signCertPwd' => $payment['upop_security_key'], //签名证书密码
    'verifyCertPath' => dirname(__FILE__).'\upop\UpopRsaCert.cer', //验签证书路径
    'merId' => $payment['upop_account'] //商户号
    */

        //正式环境参数
        'frontUrl' => 'https://101.231.204.80:5000/gateway/api/frontTransReq.do', //前台交易请求地址
        'signCertPath' => dirname(__FILE__).'\upop\700000000000001.pfx', //签名证书路径
        'signCertPwd' => $payment['upop_security_key'], //签名证书密码
        'verifyCertPath' =>dirname(__FILE__).'\upop\UpopRsaCert.cer', //验签证书路径
        'merId' => $payment['upop_account'] //商户号
    );
    	
    $unionPay->params = $_POST;
    $order_sn = trim($_GET['orderId']);


    $payment_amount = (int)$_GET['settleAmt'];

    // 检查商户账号是否一致。
    if ($payment['upop_account'] != $_GET['merId'])
    {
        ShowMsg('支付失败，请重试！',site_url('customer/order'));
        exit();
    }

    // 检查价格是否一致。
    $CI->load->model('order_model');
    	
    /* 检查支付的金额是否相符 */
    if (!$CI->order_model->check_money($order_sn, price_format($payment_amount/100)))
    {
        ShowMsg('支付失败，支付金额与订单金额不符！',site_url('customer/order'));
        exit();
    }
    	
    if ($_GET['respCode'] != '00')
    {
        ShowMsg('支付失败，请重试！错误信息：'.$_GET['respMsg'].'',site_url('customer/order'));
        exit();
    }
    	
    if($unionPay->verifySign() && $_GET['respCode'] == '00')
    {
        // 完成订单。
        $CI->order_model->order_paid($order_sn);
        ShowMsg('恭喜您订单支付成功，我们会尽快安排给您发货！',site_url('customer/order'));
        exit();
        	
    }else{

        ShowMsg('支付失败，请重试！错误信息：'.$_GET['respMsg'].'',site_url('customer/order'));
        exit();
        	
    }
    	

}



}
?>