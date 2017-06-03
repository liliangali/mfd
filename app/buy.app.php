<?php

class BuyApp extends MallbaseApp
{
    
	private $_mod_region;
	
    
	private $_mod_fabricbook;
	
    
	private $shippings = array(
        	     '1'=>'快递服务',
        	     '2'=>'门店自提',
	        );
	private $payments = array(
            	'1'=>'在线支付',
            	'2'=>'货到付款',
	         );
	
	function __construct()
	{
	    parent::__construct();
	    
		$this->_mod_region        = &m("region");
		
		$this->_mod_fabricbook    = &m("fabricbook");
	}
	
    function index()
    {
        $id      = isset($_POST['id'])      ? intval($_POST["id"])     : 0;
        
        $number  = isset($_POST['num'])  ? intval($_POST['num']) : 0;
        
        if($number <= 0)  $number = 1;
        
        $data = $this->_mod_fabricbook->get("id='{$id}' AND is_sale=1");
        
        if(empty($data)){
            
            $this->show_message("该面料册不存在或者已下架");
            
            return false;
        }
        
        if($number > $data["store"]){
            
            $this->show_message("该面料册库存 {$data["store"]}");
            
            return false;
        }
        
        $_SESSION["fabricbook"]["book"] = array(
            "id"      => $id,
            'number'  => $number,
            'price'   => $data["price"],
            'store'   => $data['store'],
            'name'    => $data["name"],
            'img'     => $data["small_img"],
            'subtotal'=> $data["price"] * $number,
        );
        
        header("Location:buy-checkout.html");
    }
    
    private function _member(){
        $member_mod = &m("member");
    
        $mInfo = $member_mod->get($this->visitor->get("user_id"));
    
        return $mInfo;
    }
    
    function checkout(){
        
        $aData = $_SESSION['fabricbook'];
        
        if(empty($aData["book"])){
            $this->show_message("没有面料册信息!");
            return false;
        }
        
        unset($aData["useCoin"]);
        unset($aData["useSurplus"]);
        $total = $this->_total($aData);
        
        $mAddr = &m('address');
        $addrs = $mAddr->find("user_id = '{$this->visitor->get("user_id")}' ORDER BY addr_id DESC");
        
        $mInfo = $this->_member();
        
        $this->assign("addresslist",  $addrs);
        
        $this->assign("count",  count($addrs));
        
        $this->assign("defAddr", $mInfo["def_addr"]);
        
        $this->assign("shippings", $this->shippings);
        
        $this->assign("payments",  $this->_payments());
        
        
        $this->assign("total", $total);
        
        $this->assign("mInfo", $mInfo);
        
        $this->assign("book", $aData["book"]);
        
        $this->_config_seo('title', "面料册购买-确认订单信息-".Conf::get("site_name")."-".Conf::get('site_title'));
        

    	$this->display('checkout.html');
    }
    
    public function coin(){
        $coin = isset($_GET['coin']) ? intval($_GET['coin']) : 0;
        if(!preg_match("/^\d+/", $coin)){
            $this->json_error("请输入正确的麦富迪币数量！");
            die();
        }
        
        $mInfo = $this->_member();
        
        if(!$mInfo["pay_password"]){
            $this->json_error("请设置支付密码！");
            die();
        }
        
        if($coin > $mInfo["coin"]){
           $coin = $mInfo["coin"];
        }
        
        $aData = $_SESSION["fabricbook"];
        $balance = floor($aData["book"]["subtotal"]-$aData['useSurplus']);

        if($balance < 0 ) $balance = 0;
        if($balance < $coin){
            $coin = $balance;
        }
        
        $aData["useCoin"] = $coin;
        $total = $this->_total($aData);
        $this->assign("total", $total);
        $content = $this->_view->fetch("total.html");
        die($this->json_result(array("content" => $content, "coin" => $total["useCoin"])));
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
    
        $aData = $_SESSION["fabricbook"];
        
        $balance = $aData["book"]["subtotal"]-$aData['useCoin'];
        
        if($balance < 0 ) $balance = 0;
        
        if($balance < $surplus){
            $surplus = $balance;
        }
        
        $aData["useSurplus"] = $surplus;
        $total = $this->_total($aData);
        $this->assign("total", $total);
        $content = $this->_view->fetch("total.html");
        $balance = $mInfo["money"] - $aData["useSurplus"];
        die($this->json_result(array("content" => $content, "surplus" => $total["useSurplus"], 'balance' => format_fee($balance))));
    }
    
    public function address(){
        $opt = isset($_GET['opt']) ? trim($_GET['opt']) : '';
        if(!in_array($opt, array("edit", "add"))){
            die($this->json_error("非法操作"));
        }
        
        $addrid = isset($_GET['addrid']) ? intval($_GET['addrid']) : 0;
        $data = array();
        $this->get_regions();
        if($opt == "edit"){
             $mAddr = &m('address');
             $data = $mAddr->get("user_id = '{$this->visitor->get("user_id")}' AND addr_id = '{$addrid}'");
             if(empty($data)){
                 die($this->json_error("非法操作"));
             }
        }
        $this->assign("data", $data);
        $content = $this->_view->fetch("address.html");
        die($this->json_result($content));
    }
    
    public function store(){
        
        $addrid = isset($_POST['addrid']) ? intval($_POST['addrid']) : 0;
        
        //验证手机号
        if(!preg_match("/^1[34578]\d{9}$/", $_POST['user_phone'])){
            $this->json_error('手机号错误!');
            die();
        }
        
        //验证地区
        if(!$_POST['area_id'] || !$_POST['area_name'] || count(explode(',', $_POST['area_id'])) != 3 ){
            $this->json_error('请正确选择地区信息!');
            die();
        }
        $model_region =& m('region');
        $_regions = $model_region->find(db_create_in($_POST['area_id'],"region_id"));
        
        $rNames = explode("\t", $_POST['area_name']);
        $rIds   = explode(",", $_POST['area_id']);
        $parent_id = 0;
        foreach ($rIds as $key=>$val){
            if($_regions[$val]['region_name'] != $rNames[$key]){
                $this->json_error('请正确选择地区信息!');
                die();
            }
            if($_regions[$val]['parent_id'] != $parent_id){
                $this->json_error('请正确选择地区信息!');
                die();
            }
            $parent_id = $val;
        }
        
        $data = array(
                'consignee'   => trim($_POST['user_name']),
                'phone_mob'   => trim($_POST['user_phone']),
                'region_id'   => trim($_POST['area_id']),
                'region_name' => trim($_POST['area_name']),
                'address'     => trim($_POST['user_adress']),
                'zipcode'     => trim($_POST['user_zipcode']),
        );
 
        $mAddress = &m("address");
        
        $addresslist = $mAddress->find(array(
            "conditions" => "user_id = '{$this->visitor->get("user_id")}'",
            "count"      => true,
            'order'      => "addr_id DESC",
        ));
        
        $count = $mAddress->getCount();
        
        if($count > 4 && !$addrid){
                die($this->json_error("最多只能添加5个收货地址"));
        }
        if($addrid){
            $where = " user_id = '{$this->visitor->get("user_id")}' AND addr_id = '{$addrid}' ";
            $res = $mAddress->edit($where,$data);
            if(isset($addresslist[$addrid])){
                $addresslist[$addrid] = $data;
            }
        }else{
            
            $data['al_name'] = '收货地址';
            $data['user_id'] = $this->visitor->get("user_id");
            $res = $mAddress->add($data);
            $data["addr_id"] = $res;
            $addresslist = array_merge(array($data),$addresslist);
        }

        if ($res < 0){
            $this->json_error('提交失败!');
            die();
        }
        
        $mInfo = $this->_member();
        $this->assign("defAddr", $mInfo["def_addr"]);
        $this->assign("addresslist", $addresslist);
        $this->assign("count", count($addresslist));
        $content = $this->_view->fetch("list.html");
        die($this->json_result($content));
    }
    
    public function destroy(){
        $addrid = isset($_GET["addrid"]) ? intval($_GET['addrid']) : 0;
        
        $mAddress = &m("address");
        
        $mAddress->drop("user_id = '{$this->visitor->get("user_id")}' AND addr_id = '{$addrid}'");
        $addresslist = $mAddress->find(array(
            "conditions" => "user_id = '{$this->visitor->get("user_id")}'",
            'order'      => "addr_id DESC",
        ));
        
        $mInfo = $this->_member();
        $this->assign("defAddr", $mInfo["def_addr"]);
        $this->assign("addresslist", $addresslist);
        $this->assign("count", count($addresslist));
        $content = $this->_view->fetch("list.html");
        die($this->json_result($content));
    }
    
    /**
     * 地区选择
     */
    private function get_regions(){
        $regions = $this->_mod_region->get_list(0);
        if ($regions)
        {
            $tmp  = array();
            foreach ($regions as $key => $value)
            {
                $tmp[$key] = $value['region_name'];
            }
            $regions = $tmp;
        }
        $this->assign("regions", $regions);
    }
    
    
    private function _total($data){
        
        $goods_amount = isset($data['book']["subtotal"]) ? $data['book']["subtotal"] : 0;
        
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
        $_SESSION["fabricbook"] = $data;
        return $result;
    }
    
    function create(){
     
        $address      = isset($_POST["addrid"])     ? intval($_POST['addrid'])     : 0;
    
        $payid        = isset($_POST["payid"])      ? intval($_POST["payid"])  : 0; 
        
        $pwd          = isset($_POST['paypwd'])     ? trim($_POST["paypwd"]) : 0;
    
        $aData        = $_SESSION['fabricbook'];
    
        $remark = isset($_POST["remark"]) ? trim($_POST['remark']) : '';
        
        $mInfo = $this->_member();
    
        $order = array(
            "remark"  => trim($data["remark"]),
        );
        
        $aInfo = array();
        if($address){
            $mAddr = &m('address');
            $aInfo = $mAddr->get("user_id = '{$this->visitor->get("user_id")}' AND addr_id = '{$address}'");
        }
        
        
        if(empty($aInfo)){
            die($this->json_error("地址不合法"));
        }
        
        $pInfo = array();
        
        if($payid){
            $pay_mod = &m("payment");
            $pInfo = $pay_mod->get("payment_id = '{$payid}'");
        }
        
        if(empty($pInfo)){
            die($this->json_error("支付方法错误"));
        }
        
        
        $_order       = array(
            'payment'   => $pInfo,
            'address'      => $aInfo,
            'remark'       => $remark,
            'user'         => $mInfo,
            'shipping'     => array(
                "shipping_id"    => 1,
                "shipping_name"  => $this->shippings[1],
            )
        );
    
        if(empty($aData['book'])){
            $this->json_error("没有面料册信息！");
            //$this->show_message("没有面料册信息！");
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
    
        $order_type =& ot("fabricbook");
    
        $transaction = $order_mod->beginTransaction();
    
        $oInfo = $order_type->submit(array(
            '_order' => $_order,
            '_cart'  => array(
                "total" => $total,
                "book"  => $aData['book'],
            ),
        ));
    
        if (!$oInfo)
        {
            /* 事务回滚 */
            $order_mod->rollback();
            $this->json_error($order_type->get_error());
            //$this->show_message($order_type->get_error());
            return;
        }
        $member_mod = &m("member");
        //使用麦富迪币 冻结
        if($total["useCoin"]){
    
            $member_mod->setDec("user_id='{$this->visitor->get("user_id")}'", "coin", $total['useCoin']);
    
            $member_mod->setInc("user_id='{$this->visitor->get("user_id")}'", "freezes_coin", $total['useCoin']);
        }
    
        if($total["useSurplus"]){
            $member_mod->setDec("user_id='{$this->visitor->get("user_id")}'", "money", $total['useSurplus']);
    
            $member_mod->setInc("user_id='{$this->visitor->get("user_id")}'", "frozen", $total['useSurplus']);
        }
    
        $order_mod->commit($transaction);
    
        if($total["order_amount"] <= 0){
            //处理订单状态
            $res = $order_type->respond_notify($oInfo["order_id"], array("target" => ORDER_ACCEPTED));    //响应通知
            if($res){
                unset($_SESSION['fabricbook']);
                $this->json_result(array(
                    "result" => "购买成功！",
                    "ordersn" => $oInfo["order_sn"],
                ));
            }else{
                $this->json_error("意外错误！");
            }
        }else{
            unset($_SESSION['fabricbook']);
            $this->json_result(array(
                "ordersn" => $oInfo["order_sn"],
            ));
        }
    
        //header("Location:buy-paycenter.html?id={$oInfo["order_sn"]}");
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
        $this->display('orders/payment/_index.html');
    }
    
    function _config_view()
    {
        parent::_config_view();
    
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
    
        $this->_view->template_dir = ROOT_PATH . "/view/".LANG."/mall/{$template_name}/fabric";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}/fabric";
        $this->_view->res_base     = SITE_URL . "/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }
  
    private function _payments(){
    
        $pay_mod = &m("payment");
    
        $payments = $pay_mod->find(array(
            'conditions' => "enabled=1 AND ismobile != 1",
            'order'      => "sort_order DESC"
        ));
    
        return $payments;
    }
    
}

?>
