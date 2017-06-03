<?php

class fx_feeApp extends MallbaseApp{

    private  $_order_serve_mod;
    
    function __construct()
    {
        parent::__construct();
        $this->_order_serve_mod = & m('orderserve');
    
    }

    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
        $this->_view->template_dir = ROOT_PATH . "/m/view/".LANG."/mall/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/m/temp/compiled/mall/{$template_name}/recharge";
        $this->_view->res_base     = SITE_URL . "/m/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }
    
    


	
	/**
     * 用户购买酷币、酷卡
	 *
     * @param string $token 用户token, int  $pageSize 页面大小, int $pageIndex 页码
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function index()
    {	
		$args = $this->get_params();
		$mod_member = m('member');
		
		$order_sn   = isset($args[0]) ? $args[0] : 0; //购买产品id（酷卡是对应酷卡log_id; 酷币对应酷币id）
		$type = isset($args[1]) ? $args[1] : 'fx'; //购买类型 kuka：酷卡 kubi：酷币
		$token   = isset($_GET['token'])   ?  trim($_GET['token']) : '';
		
		if($token){
		    $uid = intval(ApiAuthcode($token, 'DECODE', 'kuteiddiy',0)); //c9e5N2EUYiVWUUkwop9AgiQsGu3s1FPyluDLzB4Cwg
		     
		    if($uid){
		        $this->_do_login($uid);
		    }
		}
		
		if(empty($_SESSION["user_info"])){
		
		    $this->show_message("该用户是非登录状态！");
		
		    return false;
		}
        
		
		$data = $this->_order_serve_mod->get(array(
			'conditions' => "order_sn = {$order_sn}",
			'fields'     => 'id, price,order_id,order_sn,user_id,status',
		));
		
		if(empty($data['price'])){
		
		    $this->show_message("该订单返修费用不存在!");
		
		    return false;
		}
		
		if($data['status'] ==7){
		
		    $this->show_message("该订单已经支付返修费!");
		
		    return false;
		}
		
		
		
		$_SESSION["fx"]["fx"] = array(
		    'price'   => $data["price"],
		    'subtotal'=> $data["price"],
		    'type'    => $type,
		    'order_id'=> $data['order_id'],
		    'order_sn'=> $data['order_sn'],
		);
		
		header("Location:fx_fee-checkout.html");
    }
    
    function checkout(){
        $mAddr = &m('address');
    
        $addrs = $mAddr->find("user_id = '{$this->visitor->get("user_id")}'");
    
        $_order = $_SESSION['fx'];
        unset($_SESSION['fx']["useSurplus"]);
        unset($_SESSION['fx']["useCoin"]);
        $total = $this->_total();
    
        $this->assign("fx", $_order['fx']);
    
        $this->assign("address",  $addrs);
    
        $this->assign("shippings", $this->shippings);
    
        $this->assign("payments",  $this->_payments());
    
        $this->assign("mInfo",   $this->_member());
        
        $this->assign("total", $total);
    
        $this->_config_seo('title', Lang::get('confirm_order') . ' - ' . Conf::get('site_title'));
    
        $this->display('fx_checkout.html');
    }
    
     function _total(){
        $data         = isset($_SESSION['fx'])   ? $_SESSION["fx"]   : array();
    
        $goods_amount = isset($data['fx']["subtotal"]) ? $data['fx']["subtotal"] : 0;
    
        $surplus      = isset($data['useSurplus'])          ? $data['useSurplus']          : 0;
    
        $coin      = isset($data['useCoin'])          ? $data['useCoin']          : 0;
    
        $order_amount = $goods_amount;
    
        if($order_amount < $surplus){
            $_SESSION['fx']["useSurplus"] = $surplus = $order_amount;
        }
    
        $order_amount -= $surplus;
    
        if($coin > $order_amount){
            $_SESSION['fx']["useCoin"] = $coin = $order_amount;
        }
    
        $order_amount -= $coin;
    
        $result = array(
            'goods_amount'  => $goods_amount,
            'order_amount'  => $order_amount,
            'surplus'       => $surplus,
            'coin'          => $coin
        );
    
        return $result;
    }
    
    private function _payments(){
    
        $pay_mod = &m("payment");
    
        $payments = $pay_mod->find(array(
            'conditions' => "enabled=1 AND ismobile = 1",
            'order'      => "sort_order DESC"
        ));
    
        return $payments;
    }
    
    private function _member(){
        $member_mod = &m("member");
    
        $mInfo = $member_mod->get($this->visitor->get("user_id"));
    
        return $mInfo;
    }
    
    
    
    function create(){
    
        $payment_id   = isset($_POST['payment'])  ? intval($_POST['payment'])  : 0;
        $surplus      = isset($_POST['surplus'])  ? trim($_POST['surplus']) : 0;
        $coin         = isset($_POST['coin'])     ? trim($_POST["coin"]) : 0;
        $pwd          = isset($_POST['pwd'])     ? trim($_POST["pwd"]) : 0;
        $aData        = $_SESSION['fx'];
        $orderid = $aData['fx']['order_id'];
        
        // 做验证 只允许 付款一次 
        $serveinfo = $this->_order_serve_mod->get("order_id ='{$orderid}'");
        if($serveinfo['status'] ==7){
            $this->json_error("已经支付返修费用,请勿重复支付!");
            die();
        }
        
        $mInfo = $this->_member();
      
    
        if($surplus || $coin){
            if(empty($mInfo['pay_password'])){
                $this->json_error("请设置支付密码！");
                die();
            }
    
            $member_mod = &m("member");
            $mInfo = $member_mod->get($this->visitor->get("user_id"));
            $money = $mInfo["money"];
           
    
            if($money < $surplus) {
                $this->json_error("使用的金额超出最大余额数!");
                die();
            }
            
            if($mInfo['coin'] < $coin) {
                $this->json_error("使用的麦富迪币超出最大余额数!");
                die();
            }
    
            if(md5($pwd) != $mInfo["pay_password"]){
                $this->json_error("支付密码不正确！");
                die();
            }
    
            if(!preg_match("/^\d+/", $surplus) && !preg_match("/^\d+\.\d{1,2}/", $surplus)){
                $this->json_error("请输入正确的金额！");
                die();
            }
    
    
            if(!preg_match("/^\d+/", $coin) && !preg_match("/^\d+\.\d{1,2}/", $coin)){
                $this->json_error("请输入正确的麦富迪币金额！");
                die();
            }
    
            $_SESSION["fx"]["useSurplus"] = $surplus;
            $_SESSION["fx"]["useCoin"]    = $coin;
        }
    
        $_order       = $this->_order_info(array(
            'payment_id'   => $payment_id,
        ));
        

        if(empty($aData['fx'])){
            $this->show_message("没有返修费用信息！");
            return false;
        }
        
        $_order['user'] = $mInfo;
       
        $total = $this->_total();
      
 
        $order_type =& ot("fx");
        
        $transaction =  $this->_order_serve_mod->beginTransaction();
        
        $oInfo = $order_type->submit(array(
            '_order' => $_order,
            '_cart'  => array(
                "total" => $total,
                "fx"  => $aData['fx'],
            ),
        ));
        
        if (!$oInfo)
        {
            /* 事务回滚 */
            $this->_order_serve_mod->rollback();
            $this->json_error($order_type->get_error());
            return;
        }
   
         $this->_order_serve_mod->commit($transaction);
        
        if($total["order_amount"] == 0){
            //处理订单状态
            $res = $order_type->respond_notify($oInfo["order_id"], array("target" => 7));    //响应通知
            if($res){
                unset($_SESSION['fx']);
                $this->json_result(array(
                    "result" => "购买成功！",
                    "ordersn" => $oInfo["order_sn"],
                ));
            }else{
                $this->json_result(array(
                    "result" => "意外错误！"
                ));
            }
        }else{
            unset($_SESSION['fx']);
            $this->json_result(array(
                "ordersn" => $oInfo["order_sn"],
            ));
        }
    
    }
    
    function paycenter(){
       
        $sn = isset($_GET['id']) ? trim($_GET['id']) : 0;
        if(!$sn){
            $this->show_warning('参数错误！');
            return;
        }
    
        $order_mod = &m("orderserve");
      
        $order = $order_mod->get("order_sn = '{$sn}' AND user_id = '{$this->visitor->get("user_id")}'");
        if(!$order){
            $this->show_warning('参数错误！');
            return;
        }
        $this->assign('order',$order);
    
    
        if($order['status'] == 7){
            $this->display('fx/payment/haspend.html');
            return;
        }
    
        if($order['status'] != 7){
            $this->show_warning('还未支付！');
            return;
        }
    
        $pay_mod = &m("payment");
    
        $payments = $pay_mod->find("enabled=1 AND ismobile = 1");
        $this->assign('payments',$payments);
        $this->assign('obj', "order");
        $this->display('fx/payment/index.html');
    }
    
    /**
     * 去支付(APP中的去支付请求的是orderPay，然后再POST到这里了)
     *
     * @author Ruesin
     */
    function goToPay(){
    
        $obj      = isset($_POST['obj']) ? $_POST['obj'] : '';
        $order_sn = isset($_POST['os']) ?  trim($_POST['os']) : '';
    
        if(!in_array($obj,array("order"))){
            $this->assign('error_msg', '支付对象错误');
            $this->display('fx/payment/app_error.html');
            return false;
        }
    
        if (!$order_sn){
            $this->assign('error_msg', '没有找到对应订单');
            $this->display('fx/payment/app_error.html');
            return false;
        }
         
        $order = $this->_order_serve_mod->get("order_sn = '{$order_sn}'  AND user_id = '{$this->visitor->get("user_id")}'");
    
        if(!$order) {
            $this->assign('error_msg', '没有找到待支付订单');
            $this->display('fx/payment/app_error.html');
            return false;
        }
        
        $payment_model =& m('payment');
        $payment_id = $order['payment_id'];
    
        $paymentinfo = $payment_model->get($payment_id);
        //验证支付方式是否可用，若不在白名单中，则不允许使用
        if (!$payment_model->in_white_list($paymentinfo['payment_code'])){
            $this->assign('error_msg', '支付方式不可用');
            $this->display('fx/payment/app_error.html');
            return false;
        }
         
        $payment_info  = $payment_model->get("payment_code = '{$paymentinfo['payment_code']}' AND enabled=1 AND ismobile = 1");
    
        /* 没有启用，则不允许使用 */
        if (!$payment_info)
        {
            $this->assign('error_msg', '支付方式未开启');
            $this->display('fx/payment/app_error.html');
            return false;
        }
      
    
        /* 生成支付URL或表单 */
        $payment    = $this->_get_paymentm($paymentinfo['payment_code'], $payment_info);
        
        //$res = $payment->createBill($order);
        
        $paysn = $payment->createFxForAccount(array(
            'order_amount' => $order['order_amount'],
            'user_id'      => $this->visitor->get("user_id"),
            'order_id'     => $order['order_id'],
            'payment_id'   => $paymentinfo["payment_id"],
            'payment_name' => $paymentinfo['payment_name'],
            'payment_code' => $paymentinfo['payment_code']
        ));
    
    
        if(!$paysn){
            $this->assign('error_msg', '意外错误无法支付，请联系客服！');
            $this->display('fx/payment/app_error.html');
            return false;
        }
        $order['extension'] = 'fx';
        $payment_form = $payment->get_payform($order);
        $this->assign('payform', $payment_form);
        $this->assign('payment', $payment_info);
        header('Content-Type:text/html;charset=' . CHARSET);
        $this->display('/fx/payment/dopay.html');
    }
    
     function _order_info($order){
    
        $aOrder = array();
    
        if($order['payment_id']){
    
            $pay_mod = &m("payment");
    
            $aOrder["payment"] = $pay_mod->get("ismobile=1 AND payment_id='{$order["payment_id"]}'");
        }
    
        $aOrder["user"] = array(
            "user_id"    => $this->visitor->get("user_id"),
            'user_name'  => $this->visitor->get("user_name"),
        );
    
        return $aOrder;
    }
    
    private  function ApiAuthcode($string, $operation = 'DECODE', $key = 'API-mfd', $expiry = 0) {
    
        $ckey_length = 4;
    
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
    
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
    
        $result = '';
        $box = range(0, 255);
    
        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
    
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
    
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
    
        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }

}

