<?php

/**
 * 充值
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: recharge.app.php 8376 2015-09-11 02:49:48Z xugl $
 * @copyright Copyright 2014 redcollar
 */

class Kuka_buyApp extends ShoppingbaseApp{
    var $_mod_pay;
    var $_template_file = 'kuka/';
    public $types = array('kuka');
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
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
    function checkout()
    {	
		$mod_member = m('member');
		$_special_log_mod = & m('special_log');
		$_kukaconfig_mod = & m('kukaconfig');
		$coin_logmod = m('coinconfig');

		$id   = isset($_GET['kuka_id']) ? intval($_GET['kuka_id']) : 0; //购买产品id（酷卡是对应酷卡log_id; 酷币对应酷币id）
		$num  = isset($_GET['num']) ? intval($_GET['num']) : 1; //购买数量
		$type = isset($_GET['type']) ? $_GET['type'] : 'kuka'; //购买类型 kuka：酷卡 kubi：酷币
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
			$kuka_num = $_kuka_info['kuka_num'];
			
		} 
		$_SESSION['kuka'] = array(
			'id'    => $id,
		    'num'   => $num,
			'type'  => $type,
			'name'  => $name,
			'price' => $price,
			'extension' => $extension,
			'subtotal'  => $subtotal,
		    'kuka_num'  => $kuka_num,
		);
    
		//用户信息
		$_mod_member = &m('member');
    	$mInfo = $_mod_member->get($this->_user_id);
	  	
		//支付方式获取
		$_mod_pay = &m("payment");
    	$payments = $_mod_pay->find(array(
			'conditions' => "enabled=1 AND ismobile = 1",
			'order'      => "sort_order DESC"
        ));
    	//收货地址
    	$mAddr = &m('address');
    	$addrs = $mAddr->find(" user_id = '{$this->_user_id}' ");
    	$this->assign('addressList',$addrs);
    
		$this->assign('payments',$payments);
		$this->assign('args_data', $_SESSION['kuka']);
	
		$this->assign('member',$mInfo);
		$this->display($this->_template_file.'checkout.html');
    }
    
    /**
     * 更新 订单
     *
     * @author Ruesin
     */
    function update()
    {
        $ident = isset($_POST['id']) ? trim($_POST['id']) : '';
        $type  = isset($_POST['type'])? trim($_POST['type']): '';
        $num   = isset($_POST['num']) ? intval($_POST['num']) : 1;
        $kuka = $_SESSION['kuka'];
        if(!$ident || !$num ){
            $this->json_error('update_params_error');
            return;
        }
    
        if(!in_array($type, $this->types)){
            $this->json_error('type_error');
            return;
        }
   
        $_SESSION['kuka']['num'] = $num;
        
        $order_amount = $kuka['price'] * $num;
        $_SESSION['kuka']['subtotal'] = $order_amount;
        $this->json_result(array(
            'num' => $num,
            'subtotal'   => $this->_format_price_int($order_amount),//应支付金额
            'amount'   => $this->_format_price_int($order_amount),//应支付 总额
        ));
       
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
        $user   = $this->_mod_member->get($this->_user_id);
    
        ## 校验支付方式
        $order['payment'] = $this->_check_pay($_POST['payment_id']);
        
        ## 校验抵扣(余额/麦富迪币)
        $dedu = array('money'=>$_POST['yue'],'coin'=>$_POST['kubi'],'payPwd'=>$_POST['payPwd']);
        $order['pmt']    = $this->_check_pmt($dedu,$user);
        $order['quantity']    = $this->_check_num($_POST['num']);

        $_SESSION['kuka']["surplus"] = $order['pmt']['money'];
        $_SESSION['kuka']["coin"] = $order['pmt']['coin'];
        $_SESSION['kuka']["num"] =  $order['quantity']['num'];
        $_SESSION['kuka']["subtotal"] = $_SESSION['kuka']["price"] * $order['quantity']['num'];
        $_order = $order;
        $order_type  = & ot($otype);

        $total  = $this->_total();

        $aData = $_SESSION['kuka'];
        
        $_order['user'] = $user;
        

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
                $this->_mod_member->edit(" user_id = '{$this->_user_id}' ", array('money' => $money,'frozen' => $frozen));
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
        
        //处理麦富迪币
        if($total['coin'] > 0){
             
            $coin  = $_order['user']['coin'] - $total['coin'];
            $freezes_coin = $_order['user']['freezes_coin'] + $total['coin'];
            if($coin >= 0){
                $this->_mod_member->edit(" user_id = '{$this->_user_id}' ", array('coin'=>$coin,'freezes_coin'=>$freezes_coin));
            }
            $cashLog[] = array(
                'name'     => "购物(订单号：{$oInfo['order_sn']})",
                'order_id' => $oInfo['order_id'],
                'order_sn' => $oInfo['order_sn'],
                'user_id'  => $_order['user']['user_id'],
                'minus'    => 1,
                'cash_money' => $total['coin'],
                'add_time'   => time(),
                'order_money' => $total['order_amount'],
                'type'  => 2,
                'mark' => '-',
            );
             
        }

        if($cashLog) {
            $mCashLog = &mr('ordercashlog');
            $mCashLog->add(addslashes_deep($cashLog));
        }
        
        //订单日志
        imports("orderLogs.lib");
        $oLogs = new OrderLogs();


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
		$this->json_result($oInfo);
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
			
            $this->display('kuka/paycenter/success.html');
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
        $this->display('kuka/paycenter/index.html');
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
        $obj      = isset($_POST['obj']) ? $_POST['obj'] : ''; // order
        $order_sn = isset($_POST['os']) ?  trim($_POST['os']) : ''; //order_sn
       
        if(!in_array($obj,array("order"))) {
            $this->show_warning('支付对象错误!');
            return false;
        }
    
        if (!$order_sn){
            $this->show_warning('没有找到对应订单!');
            return false;
        }
         
        $order = $order = $this->_mod_order->get("order_sn = '{$order_sn}' AND status = '11' AND user_id = '{$this->_user_id}'");
    
        if(!$order) {
            $this->show_warning('参数错误!');
            return false;
        }

         if($order['status'] != ORDER_PENDING){
            $this->show_warning('订单不是待支付状态!');
            return false;
        }
         
        $payment_model =& m('payment');
        $payment_info  = $payment_model->get("payment_code = '{$order['payment_code']}' AND enabled=1 AND ismobile = 0  AND payment_code <> 'wxpay' ");
    
        /* 没有启用，则不允许使用 */
        if (!$payment_info)
        {
            $this->show_warning('支付方式未开启!');
            return false;
        }
    
        /* 生成支付URL或表单 */
        $payment    = $this->_get_payment($order['payment_code'], $payment_info);
     
        $res = $payment->createBill($order);
    
        if(!$res){
            $this->show_warning('意外错误无法支付，请联系客服！');
            return false;
        }
         
        $payment_form = $payment->get_payform($order);
      
        $this->assign('payform', $payment_form);
        $this->assign('payment', $payment_info);
        header('Content-Type:text/html;charset=' . CHARSET);
        $this->display('/kuka/paycenter/dopay.html');
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
        $surplus      = isset($data['surplus']) ? $data['surplus'] : 0;
        $coin         = isset($data['coin']) ? $data['coin'] : 0;
        $num          = isset($data['num']) ? $data['num']   : 1;
        $goods_amount = $data['price'] * $num;
        if($goods_amount < $surplus){
            $_SESSION['kuka']["surplus"] = $surplus = $goods_amount;
           
        }
        $result = array(
            'goods_amount'  => $goods_amount,//产品总金额
            'order_amount'  => $goods_amount - $surplus - $coin,//应支付金额
            'surplus'       => $surplus,//使用余额数
            'coin'          => $coin,
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
	
	
	
    
   
    
    /**
     * 校验提交确认页(还未做各种活动等的验证)
     *
     * @date 2015-8-26 上午8:19:46
     * @author Ruesin
     */
    function check(){
        if(!IS_POST){
            $this->json_error("params_error");
            return;
        }
        $cart = $this->_cart_check(1);
         
        if($cart['goods_num'] <= 0){
            $this->json_error("params_error");
            return;
        }
         
        $this->json_result(array(),'successed');
        return ;
    }

    
    /**
     * 校验支付方式
     *
     * @date 2015-9-16 下午3:16:40
     * @author Ruesin
     */
    protected function _check_pay($pay){
        $id = isset($pay['id']) ? intval($pay['id']) : 0;
        $mPay = &m("payment");
        if ($id){
            $cond = " payment_id = '{$id}' AND ";
        }
        $cond .= "  enabled=1 AND ismobile = 0  AND payment_code <> 'wxpay'  ";
        $payment = $mPay->get($cond);
    
        if(empty($payment)){
            $payment = $mPay->get("  enabled=1 AND ismobile = 0  AND payment_code <> 'wxpay'  ");
        }
    
        if (!$mPay->in_white_list($payment['payment_code']))
        {
            $this->json_error('payment_disabled_by_system');
            return;
        }
    
        return array(
            'payment_id'   => $payment['payment_id'],
            'payment_code' => $payment['payment_code'],
            'payment_name' => $payment['payment_name']
        );
    }
    
    /**
     * 校验余额/抵扣券
     *
     * @date 2015-10-20 下午3:06:13
     * @author Ruesin
     */
    function _check_pmt($dedu,$user){
    
        $coin  = (isset($dedu['coin']) && intval($dedu['coin']) >0) ? intval($dedu['coin']) : 0;
        $money = (isset($dedu['money']) && intval($dedu['money']) >0) ? intval($dedu['money']) : 0;
        $payPwd = isset($dedu['payPwd']) ? trim($dedu['payPwd']) : '';
    
        if (!$coin && !$money) return array();
    
        if( ($coin || $money) && !$payPwd ) {
         
            $this->json_error('请输入支付密码!');
            die();
        }
        if (!$user['pay_password']){
            $this->json_error('请先设置支付密码！');
            die();
        }
    
        if ($user['pay_password'] != md5($payPwd)) {
            $this->json_error('支付密码错误！');
            die();
        }
        if ($coin > $user['coin']){
            $this->json_error('积分不够啦!');
            die();
        }
    
        if($money > $user['money']){
            $this->json_error('余额不够啦!');
            die();
        }
        return array(
            'coin' => $coin,
            'money' => $money,
        );
    }
    function _check_num($num){
     
        $quantity  = (isset($num) && intval($num) >=1) ? intval($num) : 1;

        return array('num'=>$quantity);

    }
    
	


}

