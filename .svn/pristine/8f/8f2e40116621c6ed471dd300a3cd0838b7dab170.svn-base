<?php

class CoinbuyApp extends MallbaseApp
{
    
	private $coin_mod;

	function __construct()
	{
	    parent::__construct();
	    
		$this->coin_mod    = &m("cotcoin");
	}
	
    function index()
    {
        $arg = $this->get_params();
        
        $id      = isset($arg[0])      ? intval($arg[0])     : 0;
        
        $number  = 1;
        
        if(!$this->visitor->has_login){
            
            $this->show_message("该用户没有登录,只有登录的用户才能购买！");
            
            return false;
        }
        
        $data = $this->coin_mod->get("id='{$id}' AND is_sale=1");
        
        if(empty($data)){
            
            $this->show_message("该金额麦富迪币不存在或者已下架");
            
            return false;
        }
        
        $_SESSION["coin"]["coin"] = array(
            "id"      => $id,
            'number'  => $number,
            'price'   => $data["price"],
            'facevalue'    => $data["facevalue"],
            'subtotal'=> $data["price"] * $number,
            'integral' => $data["integral"],
        );

        header("Location:coinbuy-checkout.html");
    }
    
    function checkout(){
        
        $_order = $_SESSION['coin'];
        unset($_SESSION['coin']["useSurplus"]);
        unset($_SESSION['coin']["useCoin"]);
        $total = $this->_total($_order);
        
        if(empty($_order["coin"])){
            $this->show_message("没有麦富迪币信息!");
            return false;
        }
        
        $this->assign("coin", $_order['coin']);
        
        $this->assign("address",  $addrs);
        
        
        $this->assign("shippings", $this->shippings);
        
        
        $this->assign("payments",  $this->_payments());
        
        $this->assign("mInfo",   $this->_member());
        
        $this->assign("total", $total);
        
		$this->_config_seo('title', Lang::get('confirm_order') . ' - ' . Conf::get('site_title'));
		
		
    	$this->display('checkout.html');
    }
    
    
    private function _total($data){
        $goods_amount = isset($data['coin']["subtotal"]) ? $data['coin']["subtotal"] : 0;
        
        $useSurplus      = isset($data['useSurplus'])          ? $data['useSurplus']          : 0;
        
        $useCoin      = isset($data['useCoin'])          ? $data['useCoin']          : 0;
        
        $order_amount = $goods_amount;
        
        
        if($order_amount < $useSurplus){
            $data["useSurplus"] = $useSurplus = $order_amount;
        }
        
        $order_amount -= $useSurplus;
        if($order_amount < $useCoin){
            $data["useCoin"] = $useCoin = $order_amount;
        }
        
        $order_amount -= $useCoin;
        
        $result = array(
            'goods_amount'  => $goods_amount,
            'order_amount'  => $order_amount,
            'useSurplus'    => $useSurplus,
            'useCoin'       => $useCoin,
        );
        $data["order_amount"] = $order_amount;
        $_SESSION["coin"] = $data;
        
        return $result;
    }
  
    public function surplus(){
        $surplus = isset($_GET['surplus']) ? $_GET['surplus'] : 0;
    
        if(!preg_match("/^\d+/", $surplus) && !preg_match("/^\d+\.\d{1,2}$/", $surplus)){
            $this->json_error("请输入正确的金额！");
            die();
        }
        $mInfo = $this->_member();
    
        if(!$mInfo["pay_password"]){
            $this->json_error("请设置支付密码！");
            die();
        }
    
        if($surplus > $mInfo["money"]){
            $surplus = $mInfo["money"];
        }
    
        $aData = $_SESSION["coin"];
    
        $balance = $aData["coin"]["subtotal"]-$aData['useCoin'];
    
        if($balance < 0 ) $balance = 0;
    
        if($balance < $surplus){
            $surplus = $balance;
        }
        $aData["useSurplus"] = $surplus;
        $total = $this->_total($aData);
        $this->assign("total", $total);
        $content = $this->_view->fetch("total.html");
        $balance = $mInfo["money"] - $aData["useSurplus"];
        die($this->json_result(array("content" => $content, "surplus" => $total["useSurplus"], 'balance' => price_format($balance))));
    }
    
    private function _payments(){
        
        $pay_mod = &m("payment");
        
        $payments = $pay_mod->find(array(
            'conditions' => "enabled=1 AND ismobile != 1",
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
        
        $payment_id   = isset($_POST['payid'])  ? intval($_POST['payid'])  : 0;
        $pwd          = isset($_POST['paypwd'])     ? trim($_POST["paypwd"]) : 0;
        $aData        = $_SESSION['coin'];
        
        $mInfo = $this->_member();
        
        $_order       = $this->_order_info(array(
            'payment_id'   => $payment_id,
        ));
        
        if(empty($aData['coin'])){
            $this->show_message("没有麦富迪币信息！");
            return false;
        }
        
        $total = $this->_total($aData);

        if($total["useSurplus"] || $total["useCoin"]){
            if(empty($mInfo['pay_password'])){
                $this->json_error("请设置支付密码！");
                die();
            }
        
            if(md5($pwd) != $mInfo["pay_password"]){
                $this->json_error("支付密码不正确！");
                die();
            }
        }
        
        
        $order_mod = &m("order");
        
        $order_type =& ot("coin");
        
        $transaction = $order_mod->beginTransaction();
        
        $oInfo = $order_type->submit(array(
                '_order' => $_order,
                '_cart'  => array(
                         "total" => $total,
                         "coin"  => $aData['coin'],
                 ),
        ));
        
        if (!$oInfo)
        {
            /* 事务回滚 */
            $order_mod->rollback();
            $this->json_error($order_type->get_error());
            return;
        }
        
        $order_mod->commit($transaction);
        
        if($total["order_amount"] == 0){
            //处理订单状态
            $res = $order_type->respond_notify($oInfo["order_id"], array("target" => ORDER_ACCEPTED));    //响应通知
            if($res){
                unset($_SESSION['coin']);
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
            unset($_SESSION['coin']);
            $this->json_result(array(
                "ordersn" => $oInfo["order_sn"],
            ));
        }
    }
    
    function paycenter(){
        //$this->get_params();
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
            $this->display('success.html');
            return;
        }
    
        if($order['status'] != ORDER_PENDING){
            $this->show_warning('订单不是待支付状态！');
            return;
        }
    
        //货到付款
        if($order['payment_id'] == '-1'){
            $this->display('orders/payment/dispay.html');
        }
    
        $pay_mod = &m("payment");
        
        $payments = $pay_mod->find("enabled=1 AND ismobile = 1");
        $this->assign('payments',$payments);
        $this->assign('obj', "order");
        $this->display('orders/payment/index.html');
    }
    
    private function _order_info($order){
        
        $aOrder = array();
        
        if($order['payment_id']){
            
            $pay_mod = &m("payment");
            
            $aOrder["payment"] = $pay_mod->get("ismobile != 1 AND payment_id='{$order["payment_id"]}'");
        }
        
        $aOrder["user"] = array(
            "user_id"    => $this->visitor->get("user_id"),
            'user_name'  => $this->visitor->get("user_name"),
        );
        
        return $aOrder;
    }
    
    function _config_view()
    {
        parent::_config_view();

        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();

        $this->_view->template_dir = ROOT_PATH . "/view/".LANG."/mall/{$template_name}/coin";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}/coin";
        $this->_view->res_base     = SITE_URL . "/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }
    
}

?>
