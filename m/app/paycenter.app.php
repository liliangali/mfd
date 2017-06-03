<?php

/**
 *   支付中心
 *
 *    @author    yhao.bai
 */
class PaycenterApp extends PaycenterbaseApp
{
    var $_mod_pay;
    var $_mod_order;
    var $_store;
    function __construct()
    {
        parent::__construct();
        $this->_mod_pay = &m("payment");
        $this->_mod_order = &m('order');
        $this->_store = $this->visitor->get('has_store');
    }
    /**
     *    根据提供的订单信息进行支付
     *
     *    @author    yhao.bai
     *    @param    none
     *    @return    void
     */
    function index()
    {
    	/* 外部提供订单号 */
    	$args = $this->get_params();
        $order_sn = !empty($_GET) ? key($_GET) : 0;
        
        if (!$order_sn)
        {
            $this->show_warning('no_such_order');
            return;
        }
        
        /* 内部根据订单号收银,获取收多少钱，使用哪个支付接口 */
         $order = $this->_get_order($order_sn);
         
         if(!$order) return;
         
        // $this->_check_payment($order);

         $payments = $this->payments();
         $this->assign('payments',$payments);
         $this->assign('order', $order);

         $this->assign('has_store',intval($this->_store));
         $this->assign('obj', "order");
         $this->display('/paycenter/paycenter.payform.html');
    }
    
    function result(){
        $args = $this->get_params();
        $order_sn = trim($args[0]);
        $order_mod = m('order');
        //         $order = $this->_get_order($order_sn);
        $order = $order_mod->get("order_sn='$order_sn' ");
        if($order['user_id'] != $this->visitor->get("user_id")){
            $this->show_warning("非法操作");
            return false;
        }
        $this->assign("order", $order);
        $this->display("paycenter/paycenter.result.html");
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
            $this->display('order/app_error.html');
            return false;
        }
     
        if (!$order_sn){
            $this->assign('error_msg', '没有找到对应订单');
            $this->display('order/app_error.html');
            return false;
        }
         
        $order = $this->_mod_order->get("order_sn = '{$order_sn}' AND status = '11' "); //AND user_id = '{$this->_user_id}'
    
        if(!$order) {
            $this->assign('error_msg', '没有找到待支付订单');
            $this->display('order/app_error.html');
            return false;
        }
    
        $oPay = $order['payment_code'];
    
        //if($order['payment_code'] != 'malipay'){
        $order['payment_code'] = 'malipay';
        //}
    
        $payment_model =& m('payment');
         
        //验证支付方式是否可用，若不在白名单中，则不允许使用
        if (!$payment_model->in_white_list($order['payment_code'])){
            $this->assign('error_msg', '支付方式不可用');
            $this->display('order/app_error.html');
            return false;
        }
         
        $payment_info  = $payment_model->get("payment_code = '{$order['payment_code']}' AND enabled=1 AND ismobile = 1");
    
        /* 没有启用，则不允许使用 */
        if (!$payment_info)
        {
            $this->assign('error_msg', '支付方式未开启');
            $this->display('order/app_error.html');
            return false;
        }
        if ($oPay != $order['payment_code']){
            $eData = array(
                    'payment_id'   => $payment_info['payment_id'],
                    'payment_code' => $payment_info['payment_code'],
                    'payment_name' => $payment_info['payment_name']
            );
    
            $this->_mod_order->edit("order_sn = '{$order_sn}'  AND status = '".ORDER_PENDING."'" ,$eData); //AND user_id = '{$this->_user_id}'
        }
    
    
        /* 生成支付URL或表单 */
        $payment    = $this->_get_paymentm($order['payment_code'], $payment_info);
    
        $res = $payment->createBill($order);
    
        if(!$res){
            $this->assign('error_msg', '意外错误无法支付，请联系客服！');
            $this->display('order/app_error.html');
            return false;
        }
         
        $payment_form = $payment->get_payform($order);
        $this->assign('payform', $payment_form);
        $this->assign('payment', $payment_info);
        header('Content-Type:text/html;charset=' . CHARSET);
        $this->display('/orders/payment/dopay.html');

    }
	
	/**
	 * @author yhao.bai
	 * @return void
	 */
    function goToAppPay($obj, $order_sn){
    	/**
    	$order_type =& ot('normal');
    	$order_type->respond_notify(1417, array('target' => ORDER_ACCEPTED));
    	die();
    	*/
        
    	if(!in_array($obj,array("order"))){
			$this->assign('error_msg', '支付对象错误');
			$this->display('order/app_error.html');
			return false;
    	}
 
    	if (!$order_sn){
			$this->assign('error_msg', '没有找到对应订单');
			$this->display('order/app_error.html');
			return false;
    	}
    	
    	$order = $this->_get_order($order_sn);

    	if(!$order) return;

    	//$this->_check_payment($order);
    	
    	$payment_model =& m('payment');
    	
    	/* 验证支付方式是否可用，若不在白名单中，则不允许使用 
    	if (!$payment_model->in_white_list($order['payment_code']))
    	{
			$this->assign('error_msg', '支付方式不可用');
			$this->display('order/app_error.html');
			return false;
    	}*/
		//malipay = alipay
		if($order['payment_code'] == 'alipay'){
			$order['payment_code'] = 'malipay';
		}
    	
    	$payment_info  = $payment_model->get("payment_code = '{$order['payment_code']}'");
    	 
    	/* 没有启用，则不允许使用 */
    	if (!$payment_info['enabled'])
    	{
			$this->assign('error_msg', '支付方式未开启');
			$this->display('order/app_error.html');
			return false;
    	}

    	/* 生成支付URL或表单 */
    	$payment    = $this->_get_payment($order['payment_code'], $payment_info);
    
    	$res = $payment->createBill($order,$this->_store);
    	 
    	if(!$res){
			$this->assign('error_msg', '意外错误无法支付，请联系客服！');
			$this->display('order/app_error.html');
			return false;
    	}
    //print_r($payment_info);exit;	
    	$payment_form = $payment->get_payform($order);
		
    	$this->assign('payform', $payment_form);
    	$this->assign('payment', $payment_info);
    	header('Content-Type:text/html;charset=' . CHARSET);
    	$this->display('/paycenter/paycenter.dopay.html');
    }
    
    /**
     * 支付校验
     * 
     * @version 1.0.0 (Jan 20, 2015)
     * @author Ruesin
     */
    function _check_payment(&$order){
        
//         if(!$order['kh_payment_id'] || !$order['payment_id']){
//             $this->show_warning('您的订单存在异常，请联系管理员！');
//             return;
//             die();
//         }

        if($this->_store){
            if($order['status'] != ORDER_PENDING ){
                $this->show_warning('该订单不是待支付状态，请检查订单状态！');
                die();
            }
        }else{
            if($order['kh_ship_pay'] == 2){
                if($order['status'] != STORE_SHIPPED ){
                    $this->show_warning('订单不是待支付状态，请您联系裁缝！');
                    die();
                }
            }else{
                if($order['status'] != STORE_ACCEPTED ){
                    $this->show_warning('订单不是待支付状态，请您联系裁缝！');
                    die();
                }
            }
            	
            //为了后面的各个支付方式不做太大修改，用户支付时把order变量更改一下。
            $order['order_amount'] = $order['kh_order_amount'];
            $order['payment_id']   = $order['kh_payment_id'];
            $order['payment_name'] = $order['kh_payment_name'];
            $order['payment_code'] = $order['kh_payment_code'];
        }
    }
    
    /**
     * 支付方式列表
     * 
     * @version 1.0.0 (2014-12-31)
     * @author Ruesin
     */
    function payments(){
        return $this->_mod_pay->find(array(
                'conditions' => "enabled=1 AND ismobile = 1",
                'order'      => "sort_order DESC"
        ));
    }
    /**
     * Ajax 更改订单支付方式
     * 
     * @version 1.0.0 (2014-12-31)
     * @author Ruesin
     */
    function change_payment(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $sn = isset($_POST['sn']) ?  trim($_POST['sn']) : '';
        if(!$id || !$sn){
            $this->json_error('params_error');
            return ;
        }
        $payment = $this->_mod_pay->get(" payment_id = '{$id}' AND enabled=1 AND ismobile = 1");
        if(empty($payment)){
            $this->json_error('payment_error');
            return ;
        }

        if (!$this->_mod_pay->in_white_list($payment['payment_code']))
        {
            $this->json_error('payment_disabled_by_system');
            return;
        }
        if($this->_store){
            $eData = array(
                    'payment_id'   => $payment['payment_id'],
                    'payment_code' => $payment['payment_code'],
                    'payment_name' => $payment['payment_name']
            );
            $edit = $this->_mod_order->edit("order_sn = '{$sn}' AND store_id = '{$this->visitor->get('user_id')}' AND status = '".ORDER_PENDING."'" ,$eData);
        }else{
            $eData = array(
                    'kh_payment_id'   => $payment['payment_id'],
                    'kh_payment_code' => $payment['payment_code'],
                    'kh_payment_name' => $payment['payment_name']
            );
            $order = $this->_mod_order->get("order_sn = '{$sn}' AND user_id = '{$this->visitor->get('user_id')}'");
            if($order['kh_ship_pay'] == 2){
                $edit = $this->_mod_order->edit("order_sn = '{$sn}' AND user_id = '{$this->visitor->get('user_id')}' AND status = '".STORE_SHIPPED."' " ,$eData);
            }else {
                $edit = $this->_mod_order->edit("order_sn = '{$sn}' AND user_id = '{$this->visitor->get('user_id')}' AND status = '".STORE_ACCEPTED."' " ,$eData);
            }
        }
        
        if($edit >= 0){
            $this->json_result(array('nm'=>$payment['payment_name']));
            return ;
        }else{
            $this->json_error('change_payment_error');
            return ;
        }
    }

    //ns add 添加地址管理
    function address(){
        $model_address =& m('address');
        $addresses = $model_address->find("user_id=" . $this->visitor->get('user_id'));

        //默认地址
        $this->assign('def_addr',$this->visitor->get('def_addr'));
        $this->assign('count_addresses',count($addresses));
        $this->assign('addresses', $addresses);

        $this->display('/paycenter/paycenter.address.html');

    }

    //ns add 添加地址
    function address_add()
    {
        if (!IS_POST)
        {
            //读取地区
            $region_mod =& m('region');
            $this->assign('site_url', site_url());
            $this->assign('regions', $region_mod->get_options(0));

            $this->assign('act', 'address_add');
            $this->display('/paycenter/paycenter.address.form.html');
        }
        else
        {
            /* 电话和手机至少填一项 */
            if (!$_POST['phone_tel'] && !$_POST['phone_mob'])
            {
                $this->pop_warning('phone_required');

                return;
            }

            $region_name = $_POST['region_name'];
            $data = array(
                'user_id'       => $this->visitor->get('user_id'),
                'consignee'     => $_POST['consignee'],
                'region_id' => $_POST['region_list'],
                'region_name'   => $_POST['region_name'],
                'address'       => $_POST['address'],
                'phone_mob'     => $_POST['phone_mob'],
            );
            $model_address =& m('address');
            if (!($address_id = $model_address->add($data)))
            {
                $this->pop_warning($model_address->get_error());

                return;
            }
            $this->show_message('添加地址成功！','返回 >>','paycenter-address.html');
        }
    }


    //ns add 修改地址
    function address_edit(){
        $addr_id = empty($_GET['addr_id']) ? 0 : intval($_GET['addr_id']);
        if (!$addr_id)
        {
            echo Lang::get("no_such_address");
            return;
        }
        if (!IS_POST)
        {
            //读取地区
            $region_mod =& m('region');
            $this->assign('site_url', site_url());
            $this->assign('regions', $region_mod->get_options(0));

            $model_address =& m('address');
            $find_data     = $model_address->find("addr_id = {$addr_id} AND user_id=" . $this->visitor->get('user_id'));
            if (empty($find_data))
            {
                echo Lang::get('no_such_address');

                return;
            }
            $address = current($find_data);
            $this->assign('address', $address);
            $this->assign('act', 'address_edit');
            $this->display('/paycenter/paycenter.address.form.html');
        }
        else
        {
            $data = array(
                'consignee'     => $_POST['consignee'],
                'region_id' => $_POST['region_list'],
                'region_name'   => $_POST['region_name'],
                'address'       => $_POST['address'],
                'phone_mob'     => $_POST['phone_mob'],
            );
            $model_address =& m('address');
            $model_address->edit("addr_id = {$addr_id} AND user_id=" . $this->visitor->get('user_id'), $data);

            if ($model_address->has_error())
            {
                $this->pop_warning($model_address->get_error());
                return;
            }
            $this->show_message('编辑地址成功！');
        }
    }
    //ns add 删除地址
    function address_drop()
    {
        $addr_id = isset($_GET['addr_id']) ? trim($_GET['addr_id']) : 0;
        if (!$addr_id)
        {
            $this->show_warning('no_such_address');

            return;
        }
        $ids = explode(',', $addr_id);//获取一个类似array(1, 2, 3)的数组
        $model_address  =& m('address');
        $drop_count = $model_address->drop("user_id = " . $this->visitor->get('user_id') . " AND addr_id " . db_create_in($ids));
        if (!$drop_count)
        {
            /* 没有可删除的项 */
            $this->show_warning('no_such_address');

            return;
        }
        if ($model_address->has_error())    //出错了
        {
            $this->show_warning($model_address->get_error());

            return;
        }
        $this->show_message('地址删除成功！');
    }

    function edit_def_addr(){
        $args = $this->get_params();
        $def_addr_id = isset($args[0]) ? intval($args[0]) : 0;
        if($def_addr_id){
            import("address.lib");
            $this->visitor->setDef("def_addr=1", $def_addr_id); //设置默认地址
            
            //更新缓存
            $_SESSION['user_info']['def_addr'] = $def_addr_id;
            $this->show_message('默认地址设置成功！');
        }else{
            $this->show_warning('no_such_address');
            return;
        }

    }

    //ns add 预约量体信息
    function figure()
    {
        $this->display('/paycenter/paycenter.figure.html');
    }

    //ns add 获取量体数据
    function get_figure_info(){
        
        //通过输入手机号，进行查询用户量体信息（模拟数据）
        $figure_mod =& m('figure');
        $member_mod =& m('member');
        $user_info = $member_mod->get(array('conditions'=>'user_name='.$_POST['tel'].' or phone_mob='.$_POST['tel'] ));
        $figure = $figure_mod->find(array('conditions'=>'userid='.$user_info['user_id']));
        $i = 0;
        foreach($figure as $val){
            $figure_info['list'][$i] = $val;
            $i++;
        }
        $figure_info['nickname'] = $user_info['nickname'];
        $figure_info['count'] = count($figure_info['list']);
        echo ecm_json_encode($figure_info);
        return;
        //$this->assign('figure', $figure);
    }
    //ns add 发票
    function invoice(){
         $this->display('/paycenter/paycenter.invoice.html');
    }



}

