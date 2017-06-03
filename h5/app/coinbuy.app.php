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
        
//         $_GET = array(
//             "id" => 1,
//             "number" => 1,
//             "token"  => "029b8323548a50e2529dd7fb05bb74ce"
//         );
        
        $id      = isset($_GET['id'])      ? intval($_GET["id"])     : 0;
        
        $number  = isset($_GET['number'])  ? intval($_GET['number']) : 0;
        
        $token   = isset($_GET['token'])   ?  trim($_GET['token']) : '';
        
       // $token = $this->ApiAuthcode("1011", "ENCODE",'kuteiddiy');
        
      // $_GET['token'] = $token;
        
        if($token){
            $uid = intval($this->ApiAuthcode($token, 'DECODE', 'kuteiddiy',0)); //c9e5N2EUYiVWUUkwop9AgiQsGu3s1FPyluDLzB4Cwg
           
            if($uid){
                $this->_do_login($uid);
            }
        }
       
        if(empty($_SESSION["user_info"])){
            
            $this->show_message("该用户是非登录状态！");
            
            return false;
        }
        

        if($number <= 0)  $number = 1;
        
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
        $mAddr = &m('address');
        
        $addrs = $mAddr->find("user_id = '{$this->visitor->get("user_id")}'");
        
        $_order = $_SESSION['coin'];
        unset($_SESSION['coin']["useSurplus"]);
        unset($_SESSION['coin']["useCoin"]);
        $total = $this->_total();
        
        $this->assign("coin", $_order['coin']);
        
        $this->assign("address",  $addrs);
        
        
        $this->assign("shippings", $this->shippings);
        
        
        $this->assign("payments",  $this->_payments());
        
        $this->assign("mInfo",   $this->_member());
        
        $this->assign("total", $total);
        
		$this->_config_seo('title', Lang::get('confirm_order') . ' - ' . Conf::get('site_title'));
		
		
    	$this->display('coinbuy_checkout.html');
    }
    
    
    private function _total(){
        $data         = isset($_SESSION['coin'])   ? $_SESSION["coin"]   : array();
        
        $goods_amount = isset($data['coin']["subtotal"]) ? $data['coin']["subtotal"] : 0;
        
        $surplus      = isset($data['useSurplus'])          ? $data['useSurplus']          : 0;
        
        $coin      = isset($data['useCoin'])          ? $data['useCoin']          : 0;
        
        $order_amount = $goods_amount;
        
        if($order_amount < $surplus){
            $_SESSION['coin']["useSurplus"] = $surplus = $order_amount;
        }

        $order_amount -= $surplus;
        
        if($coin > $order_amount){
            $_SESSION['coin']["useSurplus"] = $coin = $order_amount;
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
        $aData        = $_SESSION['coin'];
        
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
            
            $_SESSION["coin"]["useSurplus"] = $surplus;
            $_SESSION["coin"]["useCoin"]    = $coin;
        }
        
        $_order       = $this->_order_info(array(
            'payment_id'   => $payment_id,
        ));
        
        if(empty($aData['coin'])){
            $this->show_message("没有面料册信息！");
            return false;
        }
        
        $total = $this->_total();

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
        $auth_mod  = &m('auth');
        $order = $order_mod->get("order_sn = '{$sn}' AND user_id = '{$this->visitor->get("user_id")}'");
        if(!$order){
            $this->show_warning('参数错误！');
            return;
        }
        $this->assign('order',$order);
    
    
        if($order['status'] == ORDER_ACCEPTED){
            $authinfo = $auth_mod->get("user_id ='{$order['user_id']}'");
           // 验证此用户 是否实名认证过
            $this->assign('auth_id',$authinfo['id']);
            $this->display('orders/payment/haspend.html');
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
            
            $aOrder["payment"] = $pay_mod->get("ismobile=1 AND payment_id='{$order["payment_id"]}'");
        }
        
        $aOrder["user"] = array(
            "user_id"    => $this->visitor->get("user_id"),
            'user_name'  => $this->visitor->get("user_name"),
        );
        
        return $aOrder;
    }
    
    private function ApiAuthcode($string, $operation = 'DECODE', $key = 'API-mfd', $expiry = 0) {
    
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

?>