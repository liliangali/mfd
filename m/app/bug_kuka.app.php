<?php

/**
 * 充值
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: recharge.app.php 8376 2015-09-11 02:49:48Z xugl $
 * @copyright Copyright 2014 redcollar
 */

class bug_kukaApp extends MallbaseApp{
    var $_mod_pay;
    
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
        $this->_view->template_dir = ROOT_PATH . "/m/view/".LANG."/mall/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/m/temp/compiled/mall/{$template_name}/recharge";
        $this->_view->res_base     = SITE_URL . "/m/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
		$this->_mod_pay   = &m('payment');
		$this->_mod_order = &m("order");
		$this->_mod_member= &m('member');
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
		$_special_log_mod = & m('special_log');
		$_kukaconfig_mod = & m('kukaconfig');
		$coin_logmod = m('coinconfig');

		$id   = isset($args[0]) ? intval($args[0]) : 0; //购买产品id（酷卡是对应酷卡log_id; 酷币对应酷币id）
		$num  = isset($args[1]) ? intval($args[1]) : 1; //购买数量
		$type = isset($args[2]) ? $args[2] : ''; //购买类型 kuka：酷卡 kubi：酷币
		$token= isset($_GET['token'])   ?  trim($_GET['token']) : '';
		
		if($token){
		    $uid = intval($this->ApiAuthcode($token, 'DECODE', 'kuteiddiy',0)); //c9e5N2EUYiVWUUkwop9AgiQsGu3s1FPyluDLzB4Cwg
		    
		    if($uid){
		        $this->_do_login($uid);
		    }
		}

		$final_amount = 0;
		$price = 0;
		if($type == 'kuka') {//酷卡
			$extension = 'kuka';
		
			$_kuka_info = $_kukaconfig_mod->get(array(
				'conditions' => "id = {$id}",
				'fields'     => 'id, kuka_name, kuka_num, sale_price',
			));
			$subtotal = $_kuka_info['sale_price'] * $num;
			$price    = $_kuka_info['sale_price'];
			$name     = $_kuka_info['kuka_name'];
		} 

		$_SESSION['kuka'] = array(
			'id'    => $id,
			'num'   => $num,
			'type'  => $type,
			'name'  => $name,
			'price' => $price,
			'extension' => $extension,
			'subtotal'  => $subtotal,
		);
		header("Location:bug_kuka-checkout.html");
		
    }
    
    function checkout(){
        //用户信息
        $mInfo =  $this->_member();
        
        //支付方式获取
        $_mod_pay = &m("payment");
        $payments = $_mod_pay->find(array(
            'conditions' => "enabled=1 AND ismobile = 1",
            'order'      => "sort_order DESC"
        ));
        //var_dump($_SESSION['kuka']); exit;
        $this->assign('payments',$payments);
        $this->assign('args_data', $_SESSION['kuka']);
        $this->assign('member',$mInfo);
        $this->display('kuka/buy_kuka.form.html');
    }
	
	/**
	 * 创建酷卡、酷币订单
	 *
	 * @param NULL
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	function create(){
        
        $otype = 'kuka';
		$payment_id = isset($_POST['payment'])  ? intval($_POST['payment'])  : 0;
        $aData = $_SESSION['kuka'];
        $user   = $this->_mod_member->get($this->visitor->get("user_id"));
        $total  = $this->_total();
        $_order['user'] = $user;
        
        if($payment_id){
            $payment = $this->_mod_pay->get("payment_code = '{$payment_id}' AND enabled=1 AND ismobile = 1");
        }
        if(!$payment_id || !$payment){
            $payment = $this->_mod_pay->get(array(
				'conditions' => "enabled=1 AND ismobile = 1",
				'order'      => "sort_order DESC"
            ));
        }
        
        if(empty($aData)){
            $this->show_message("没有酷卡信息,请重新购买！");
            return false;
        }

        $_order['payment'] = $payment;
        $order_type  = & ot($aData['type']);
       
        $transaction = $this->_mod_order->beginTransaction();

        //生成订单
        $oInfo = $order_type->submit(array(
			'_order' => $_order,
			'total'  => $total,
			'kuka'  => $aData,
        ));
       
        if (!$oInfo) {
            $this->_mod_order->rollback();
            $this->json_error($order_type->get_error());
            return;
        }   
        $this->_mod_order->commit($transaction);

        //处理余额 
        if($total['surplus']) {
            $money  = $_order['user']['money'] - $total['surplus'];
            $frozen = $_order['user']['frozen'] + $total['surplus'];
            if($money >= 0){
                $this->_mod_member->edit(" user_id = '{$this->visitor->get("user_id")}' ", array('money' => $money,'frozen' => $frozen));
            }
            
            $cashLog[] = array(
				'name'        => "购物(订单号：{$oInfo['order_sn']})",
				'order_id'    => $oInfo['order_id'],
				'order_sn'    => $oInfo['order_sn'],
				'user_id'     => $_order['user']['user_id'],
				'minus'       => 1,
				'cash_money'  => $total['money_amount'],
				'add_time'    => time(),
				'order_money' => $total['surplus'],
				'type'        => 4,
				'mark'        => '-',
            );
            
        }

        if($cashLog) {
            $mCashLog = &mr('ordercashlog');
            $mCashLog->add(addslashes_deep($cashLog));
        }
        
        //订单日志
        imports("orderLogs.lib");
        $oLogs = new OrderLogs();
        /*
        $oLogs->_record(array(
                'order_id' => $oInfo['order_id'],
                'op_id'    => $_order['user']['user_id'],
                'op_name'  => $_order['user']['user_name'],
                'behavior' => 'create',
                'from'     => '',
                'to'       => ORDER_PENDING,
        ));*/

        if($oInfo['status'] != ORDER_PENDING){
			
            $oLogs->_record(array(
				'order_id' => $oInfo['order_id'],
				'op_id'    => $_order['user']['user_id'],
				'op_name'  => $_order['user']['user_name'],
				'behavior' => 'payment',
				'from'     => ORDER_PENDING,
				'to'       => ORDER_ACCEPTED,
				'text'     => '订单创建并支付成功!', 
            ));
            if ($oInfo['status'] == ORDER_WAITFIGURE){
                $oLogs->_record(array(
					'order_id' => $oInfo['order_id'],
					'op_id'    => $_order['user']['user_id'],
					'op_name'  => $_order['user']['user_name'],
					'behavior' => 'payment',
					'from'     => ORDER_ACCEPTED,
					'to'       => ORDER_WAITFIGURE,
					'text'     => '订单创建支付成功!'
                ));
            }elseif ($oInfo['status'] == ORDER_PRODUCTION){
                $oLogs->_record(array(
					'order_id' => $oInfo['order_id'],
					'op_id'    => $_order['user']['user_id'],
					'op_name'  => $_order['user']['user_name'],
					'behavior' => 'payment',
					'from'     => ORDER_ACCEPTED,
					'to'       => ORDER_PRODUCTION,
					'text'     => '订单创建支付成功，生产中。'
                ));
            }
        }
		unset($_SESSION["kuka"]);
        header("Location:bug_kuka-paycenter.html?id={$oInfo["order_sn"]}");
    }
	
	/**
	 * 支付中心
	 *
	 * @param NULL
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	function paycenter() {
        $sn = isset($_GET['id']) ? trim($_GET['id']) : 0;
        if(!$sn){
            $this->show_warning('参数错误！');
            return;
        }
    
        $order_mod = &m("order");
        
        $order = $order_mod->get("order_sn = '{$sn}' AND user_id = '{$this->visitor->get("user_id")}'");
        if(!$order){
            $this->show_warning('参数错误！');
            return;
        }
        $this->assign('order',$order);
    
    
        if($order['status'] == ORDER_ACCEPTED){
		
			//如果支付成功。则处理对应数据
			$bill_info = array(
				'member_id' => $this->visitor->get("user_id"),
				'type'      => $order['extension'],
				'order_id'  => $order['order_id'],
			);
			if(!$this->_change_coin($bill_info)){
				$this->show_warning('数据转换失败！');
				return;
			}
			
            $this->display('kuka/payment/success.html');
            return;
        }
    
        if($order['status'] != ORDER_PENDING){
            $this->show_warning('订单不是待支付状态！');
            return;
        }
    
        $pay_mod = &m("payment");
        $mCashLog = &mr('ordercashlog');
        $cashinfo = $mCashLog->get("order_id = '{$order['order_id']}'");
        $this->assign('cashinfo', $cashinfo);
        $payments = $pay_mod->find("enabled=1 AND ismobile = 1");
        $this->assign('payments', $payments);
        $this->assign('obj', "order");
        $this->display('kuka/payment/index.html');
    }
	
	/**
     *    购买酷卡、酷币修改酷币值
     *
     *    @author    Ruesin
     */
    function _change_coin($info = array()) {
	
		$mod_member  = &m('member');
		$transaction = $mod_member->beginTransaction();
		$res = $mod_member->recharge_coin($info);  	
		if ($res['code']) {
			$mod_member->rollback();
			return false;
		}
		$mod_member->commit($transaction);
		return true;
    }
    
    function goToPay(){
        $obj      = isset($_POST['obj']) ? $_POST['obj'] : '';
        $order_sn = isset($_POST['os']) ?  trim($_POST['os']) : '';
    
        if(!in_array($obj,array("order"))) {
            $this->assign('error_msg', '支付对象错误');
            $this->display('kuka/app_error.html');
            return false;
        }
    
        if (!$order_sn){
            $this->assign('error_msg', '没有找到对应订单');
            $this->display('kuka/app_error.html');
            return false;
        }
         
        $order = $order = $this->_mod_order->get("order_sn = '{$order_sn}' AND status = '11' AND user_id = '{$this->visitor->get("user_id")}'");
    
        if(!$order) {
            $this->assign('error_msg', '没有找到待支付订单');
            $this->display('kuka/app_error.html');
            return false;
        }
         
        $payment_model =& m('payment');
        $payment_info  = $payment_model->get("payment_code = '{$order['payment_code']}' AND enabled=1 AND ismobile = 1");
    
        /* 没有启用，则不允许使用 */
        if (!$payment_info)
        {
            $this->assign('error_msg', '支付方式未开启');
            $this->display('kuka/app_error.html');
            return false;
        }
    
        /* 生成支付URL或表单 */
        $payment    = $this->_get_paymentm($order['payment_code'], $payment_info);
     
        $res = $payment->createBill($order);
    
        if(!$res){
            $this->assign('error_msg', '意外错误无法支付，请联系客服！');
            $this->display('kuka/app_error.html');
            return false;
        }
         
        $payment_form = $payment->get_payform($order);
        $this->assign('payform', $payment_form);
        $this->assign('payment', $payment_info);
        header('Content-Type:text/html;charset=' . CHARSET);
        $this->display('/kuka/payment/dopay.html');
    }
	
	/**
	 * 异步处理计算总价格
	 *
	 * @param int surplus 使用余额支付金额
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	function surplus() {
		
        $surplus = isset($_POST["surplus"]) ? intval($_POST["surplus"]) : 0;
        $kuka = $_SESSION['kuka'];

		$member_mod = &m("member");
        $mInfo = $member_mod->get($this->visitor->get("user_id"));
        $money = $mInfo["money"];
        
        if($money < $surplus) {
            $this->json_error("使用的金额超出最大余额数!");
            die();
        }
        
        $return = 0;
        if($surplus > $kuka['subtotal']) {
            $surplus = $kuka['subtotal'];
            $return  =  1;
        }
		
        $_SESSION['kuka']['surplus'] = $surplus;
        
		$order_amount = $kuka['subtotal'] - $surplus;
     
		$this->json_result(array(
			'returned' => $return ? $surplus : 0,
            'surplus'  => $this->_format_price_int($surplus),
            'amount'   => $this->_format_price_int($order_amount),//应支付金额
        ));
    }
	
	/**
	 * 计算金额
	 *
	 * @param NULL
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	 function _total() {
        $data         = isset($_SESSION['kuka']) ? $_SESSION["kuka"]   : array();
        $goods_amount = isset($data["subtotal"]) ? $data["subtotal"] : 0;
        $surplus      = isset($data['surplus']) ? $data['surplus']          : 0;
        
        if($goods_amount < $surplus){
            $_SESSION['kuka']["surplus"] = $surplus = $goods_amount;
        }
        $result = array(
            'goods_amount'  => $goods_amount,//产品总金额
            'order_amount'  => $goods_amount - $surplus,//应支付金额
            'surplus'       => $surplus,//使用余额数
        );
        return $result;
    }
	
	/**
	 * 异步判断用户是否设置支付密码
	 *
	 * @param NULL
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	function checkPayset() {
		$member_mod = &m("member");
        $mInfo = $member_mod->get($this->visitor->get("user_id"));

	    if(!$mInfo['pay_password']) {
            $this->json_error("为了您的账户资金安全，余额支付时需要启用支付密码!");die();
        }

		$this->json_result('', '支付密码输入正确！');
	}
	
	/**
	 * 异步判断用户支付密码输入
	 *
	 * @param int paypwd  支付密码
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	function checkPaypwd() {
		$paypwd = isset($_POST["paypwd"]) ? $_POST["paypwd"] : 0;
		$member_mod = &m("member");
        $mInfo = $member_mod->get($this->visitor->get("user_id"));



	    if(!$mInfo['pay_password'] || $mInfo['pay_password'] != md5($paypwd) ) {
            $this->json_error("支付密码错误!");die();
        }
		
		$this->json_result('', '支付密码输入正确！');
	}
	
	
	private function _member(){
	    $member_mod = &m("member");
	
	    $mInfo = $member_mod->get($this->visitor->get("user_id"));
	
	    return $mInfo;
	}
    
    
    /**
     * 支付方式列表
     *
     * @version 1.0.0 (2015-03-12)
     * @author Ruesin
     */
    function payments(){
        return $this->_mod_pay->find(array(
                'conditions' => "enabled=1 AND ismobile = 0",
                'order'      => "sort_order DESC"
        ));
    }
    
	/**
	 * 价格格式处理（保留小数点后两位00）
	 *
	 * @param String $price 
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	function _format_price_int($price = 0.00)
	{
		return sprintf('%.2f',intval($price));
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

