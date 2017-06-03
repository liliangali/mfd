<?php

/**
 * 支付返修费用
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: recharge.app.php 8376 2015-09-11 02:49:48Z tangsj $
 * @copyright Copyright 2014 redcollar
 */

class FxfeeApp extends ShoppingbaseApp{
    var $_mod_pay;
    var $_mod_member;
    var $_mod_order;
    var $_template_file = 'fxfee/';
    var $_order_serve_mod;
    public $types = array('fx');
    
    function __construct()
    {
        parent::__construct();
        $this->_order_serve_mod = & m('orderserve');
        $this->_mod_pay   = &m('payment');
        $this->_mod_order = &m("order");
        $this->_mod_member= &m('member');
    
    }
    
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
    
    }
    

    function index(){
        
     
        $id   = isset($_GET['id']) ? intval($_GET['id']) : 0; 
  
        if(!$this->visitor->has_login){
        
            $this->show_message("该用户没有登录,只有登录的用户才能付费！");
        
            return false;
        }
        
        $data = $this->_order_serve_mod->get($id);
      
        
        if(empty($data['price'])){
        
            $this->show_message("该订单返修费用还未设置!");
        
            return false;
        }
        
        if($data['status'] ==7){
        
            $this->show_message("该订单已经支付返修费!");
        
            return false;
        }
        
        $_SESSION["fx"]["fx"] = array(
            'price'   => $data["price"],
            'subtotal'=> $data["price"],
            'type'    => 'fx',
            'order_id'=> $data['order_id'],
            'order_sn'=> $data['order_sn'],
        );
        
        header("Location:fxfee-checkout.html");     
    }

	/**
     * 用户支付返修费用
     * @access protected
     * @author tangsj <963830611@qq.com>
     * @return void
     */
    function checkout()
    {	
        $aData = $_SESSION['fx']['fx'];
        if(empty($aData)){
            $this->show_message("返修费用信息!");
            return false;
        }
        
        unset($aData["surplus"]);
        unset($aData["coin"]);
        $total = $this->_total($aData);
     
		//用户信息
		$_mod_member = &m('member');
    	$mInfo = $_mod_member->get($this->_user_id);
	  	
		//支付方式获取
		$_mod_pay = &m("payment");
    	$payments = $_mod_pay->find(array(
			'conditions' => "enabled=1 AND ismobile != 1",
			'order'      => "payment_id ASC"
        ));
    	//收货地址
    	$mAddr = &m('address');
    	$addrs = $mAddr->find(" user_id = '{$this->_user_id}' ");
    	$this->assign('addressList',$addrs);
    
		$this->assign('payments',$payments);
		$this->assign('args_data', $aData);
		$this->assign("total", $total);
	
		$this->assign('member',$mInfo);
		$this->display($this->_template_file.'checkout.html');
    }
    

	
	/**
	 * 返修费用 清单
	 *
	 * @param NULL
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
    function create(){
        $payment_id   = isset($_POST['payment_id'])  ? intval($_POST['payment_id'])  : 0;
        $surplus      = isset($_POST['yue'])  ? trim($_POST['yue']) : 0;
        $coin         = isset($_POST['kubi'])     ? trim($_POST["kubi"]) : 0;
        $pwd          = isset($_POST['payPwd'])     ? trim($_POST["payPwd"]) : 0;
        $aData        = $_SESSION['fx'];
        $orderid = $aData['fx']['order_id'];
    
        // 做验证 只允许 付款一次
        $serveinfo = $this->_order_serve_mod->get("order_id ='{$orderid}'");
        if($serveinfo['status'] ==7){
            $this->json_error("已经支付返修费用,请勿重复支付!");
            die();
        }
        
        $member_mod = &m("member");
        $mInfo = $member_mod->get($this->visitor->get("user_id"));
    
    
        if($surplus || $coin)
        {
            if(!$pwd) {
                 
                $this->json_error('请输入支付密码!');
                die();
            }
            if(empty($mInfo['pay_password']))
            {
                $this->json_error("请设置支付密码！");
                die();
            }
    
            $money = $mInfo["money"];
             
    
            if($money < $surplus)
            {
                $this->json_error("使用的金额超出最大余额数!");
                die();
            }
    
            if($mInfo['coin'] < $coin)
            {
                $this->json_error("使用的麦富迪币超出最大余额数!");
                die();
            }
    
            if(md5($pwd) != $mInfo["pay_password"])
            {
                $this->json_error("支付密码不正确！");
                die();
            }
    
            if(!preg_match("/^\d+/", $surplus) && !preg_match("/^\d+\.\d{1,2}/", $surplus))
            {
                $this->json_error("请输入正确的金额！");
                die();
            }
    
    
            if(!preg_match("/^\d+/", $coin) && !preg_match("/^\d+\.\d{1,2}/", $coin))
            {
                $this->json_error("请输入正确的麦富迪币金额！");
                die();
            }
    
            $_SESSION["fx"]["useSurplus"] = $surplus;
            $_SESSION["fx"]["useCoin"]    = $coin;
        }
        
        $_order = $this->_order_info(array(
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
                'total'  => $total,
                'fx'     => $aData['fx'],
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
                    "order_sn" => $oInfo["order_sn"],
                ));
            }else{
                $this->json_result(array(
                    "result" => "意外错误！"
                ));
            }
        }else{
            unset($_SESSION['fx']);
            $this->json_result(array(
                "order_sn" => $oInfo["order_sn"],
            ));
        }
    
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
    
        $order = $this->_order_serve_mod->get("order_sn = '{$sn}' AND user_id = '{$this->visitor->get("user_id")}'");
       
        if(!$order){
            $this->show_warning('参数错误！');
            return;
        }
        
        $this->assign('order',$order);
        

        if($order['status'] == 7){
        
            $this->display('fxfee/paycenter/success.html');
            return;
        }
        
        $pay_mod = &m("payment");
        $mCashLog = &mr('ordercashlog');
        $cashinfo = $mCashLog->get("order_id = '{$order['order_id']}'");
        $this->assign('cashinfo', $cashinfo);
        $payments = $pay_mod->find("enabled=1 AND ismobile = 1");
        $this->assign('payments', $payments);
        $this->assign('obj', "order");
        $this->display('fxfee/paycenter/index.html');
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
            $this->show_warning('支付对象错误!');
            return false;
        }
    
        if (!$order_sn){
            $this->show_warning('没有找到对应订单!');
            return false;
        }
         
        $order = $this->_order_serve_mod->get("order_sn = '{$order_sn}'  AND user_id = '{$this->_user_id}'");
      
        if(!$order) {
            $this->show_warning('没有找到待支付订单!');
            return false;
        }
    
        $payment_model =& m('payment');
        $payment_id = $order['payment_id'];
    
        $paymentinfo = $payment_model->get($payment_id);
        //验证支付方式是否可用，若不在白名单中，则不允许使用
        if (!$payment_model->in_white_list($paymentinfo['payment_code'])){
            $this->show_warning('支付方式不可用!');
            return false;
        }
         
        $payment_info  = $payment_model->get("payment_code = '{$paymentinfo['payment_code']}' AND enabled=1 AND ismobile != 1");
      
        /* 没有启用，则不允许使用 */
        if (!$payment_info)
        {
            $this->show_warning('支付方式未开启!');
            return false;
        }
    
    
        /* 生成支付URL或表单 */ 
        $payment = $this->_get_payment($paymentinfo['payment_code'], $payment_info);
       
    
        $paysn = $payment->createFxForAccount(array(
            'order_amount' => $order['order_amount'],
            'user_id'      => $this->visitor->get("user_id"),
            'order_id'     => $order['order_id'],
            'payment_id'   => $paymentinfo["payment_id"],
            'payment_name' => $paymentinfo['payment_name'],
            'payment_code' => $paymentinfo['payment_code']
        ));
    
    
        if(!$paysn){
            $this->show_warning('意外错误无法支付，请联系客服！');
            return false;
        }
        $order['extension'] = 'fx';
        $payment_form = $payment->get_payform($order, $order['extension']);
        $this->assign('payform', $payment_form);
        $this->assign('payment', $payment_info);
        header('Content-Type:text/html;charset=' . CHARSET);
        $this->display('/fxfee/paycenter/dopay.html');
    }
    
    
    
    
	

	

	
	/**
	 * 计算金额
	 *
	 * @param NULL
	 *
	 * @access protected
	 * @author tangsj <963830611@qq.com>
	 * @return void
	 */
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
	

	function _order_info($order){
	
	    $aOrder = array();
	
	    if($order['payment_id']){
	
	        $pay_mod = &m("payment");
	
	        $aOrder["payment"] = $pay_mod->get("ismobile !=1 AND payment_id='{$order["payment_id"]}'");
	    }
	
	    $aOrder["user"] = array(
	        "user_id"    => $this->visitor->get("user_id"),
	        'user_name'  => $this->visitor->get("user_name"),
	    );
	
	    return $aOrder;
	}
}

