<?php
/**
 *    申请面料控制器
 */
class Fabric_buyApp extends ShoppingbaseApp{
    var $part_mod;
    var $store_fabric_mod;
    var $part_attribute_mod;
    var $part_attr_mod;
	var $_template_file = 'fabric/';
	function __construct()
    {
        $this->Fabric_buyApp();

    }
    

    function Fabric_buyApp()
    {
        parent::__construct();
		$this->_mod_region        = &m("region");
		$this->_mod_fabricbook    = &m("fabricbook");
    }
	
	/**
	 * 面料购买申请
	 *
	 * @param int category 筛选面料册分类
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
    function checkout()
	{
        //获得面料信息
        $id      = isset($_POST['fabricId'])  ? intval($_POST["fabricId"])     : 0;
        $number  = isset($_POST['num'])  ? intval($_POST['num']) : 1;

        $data = $this->_mod_fabricbook->get("id='{$id}' AND is_sale=1");
        
        if(empty($data)) {
            $this->show_message("该面料册不存在或者已下架");
            return false;
        }
        
        if($number > $data["store"]) {
            $this->show_message("该面料册库存 {$data["store"]}");
            return false;
        }
		
        $_SESSION["fabricbook"]["book"] = array(
            "id"       => $id,
            'number'   => $number,
            'price'    => $data["price"],
            'store'    => $data['store'],
            'name'     => $data["name"],
            'img'      => $data["small_img"],
            'subtotal' => $data["price"] * $number,
        );
	
		$this->assign("fabric_data", $_SESSION["fabricbook"]["book"]);
    
        //会员信息
        $_mod_member = &m('member');
        $mInfo = $_mod_member->get($this->_user_id);
        $this->assign('member',$mInfo);
        
        //收货地址
        $mAddr = &m('address');
        $addrs = $mAddr->find(" user_id = '{$this->_user_id}' ");
        $this->assign('addressList',$addrs);
        
        //支付方式
        $this->assign("payments", $this->payments());
    	
		$this->_config_seo('title', Lang::get('confirm_order') . ' - ' . Conf::get('site_title'));
    	$this->display($this->_template_file.'checkout.html');
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
        
        $book = $_SESSION['fabricbook']['book'];
        
        $money = $this->_memberSurplus();
        
        if($money < $surplus) {
            $this->json_error("使用的金额超出最大余额数!");
            die();
        }
        
        $return = 0;
        if($surplus > $book['subtotal']) {
            $surplus = $book['subtotal'];
            $return  =  1;
        }
        
        $_SESSION['fabricbook']['surplus'] = $surplus;
        
		$order_amount = $book['subtotal'] - $surplus;
     
		$this->json_result(array(
			'returned' => $return ? $surplus : 0,
            'surplus'  => $surplus,
            'amount'   => $order_amount,//应支付金额
        ));
    }
	
	/**
	 * 创建面料订单
	 *
	 * @param NULL
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	function create() {
		$shipping_id  = isset($_POST['shipping_id']) ? intval($_POST['shipping_id']) : 1;
        $payment_id   = isset($_POST['payment_id'])  ? intval($_POST['payment_id'])  : 0;
        $address      = isset($_POST["address"])     ? intval($_POST['address'])     : 0;
        $aData        = $_SESSION['fabricbook'];
        $_order       = $this->_order_info(array(
            'payment_id'   => $payment_id,
			'shipping_id'  => $shipping_id,
            'address'      => $address, 
        ));

        if(empty($aData['book'])){
            $this->json_error("没有面料册信息！");
            return false;
        }
   
        $total = $this->_total();
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

        if (!$oInfo) {
            /* 事务回滚 */
            $order_mod->rollback();
            $this->json_error($order_type->get_error());
            return;
        }
        
        $order_mod->commit($transaction);
        if($total["order_amount"] <= 0){
            $this->_mod_fabricbook->setDec("id='{$aData['book']["id"]}'", "store", $aData['book']["number"]);
        }
        
        if($total['surplus']){
            $member_mod = &m("member");
            $member_mod->setDec("user_id='{$this->visitor->get("user_id")}'", 'money', $total['surplus']);
        }
        
        unset($_SESSION['fabricbook']);
        
        //$this->json_result($oInfo);
       // die();
        
        header("Location:fabric_buy-paycenter.html?id={$oInfo["order_sn"]}");
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
        $data         = isset($_SESSION['fabricbook'])   ? $_SESSION["fabricbook"]   : array();
        $goods_amount = isset($data['book']["subtotal"]) ? $data['book']["subtotal"] : 0;
        $surplus      = isset($data['surplus'])          ? $data['surplus']          : 0;
        
        if($goods_amount < $surplus){
            $_SESSION['fabricbook']["surplus"] = $surplus = $goods_amount;
        }
        $result = array(
            'goods_amount'  => $goods_amount,//产品总金额
            'order_amount'  => $goods_amount - $surplus,//应支付金额
            'surplus'       => $surplus,//使用余额数
        );
        return $result;
    }
	
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
            $this->display('order/paycenter/success.html');
            return;
        }
    
        if($order['status'] != ORDER_PENDING){
            $this->show_warning('订单不是待支付状态！');
            return;
        }
    
        $pay_mod = &m("payment");
        
        $payments = $pay_mod->find("enabled=1 AND ismobile = 0");
        $this->assign('payments',$payments);
        $this->assign('obj', "order");
        $this->display('order/paycenter/index.html');
    }
	
	/**
	 * 组装订单信息
	 *
	 * @param NULL
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	 function _order_info($order){
        
        $aOrder = array(); 
		if($order["shipping_id"]){
            
            $aOrder['shipping'] = array(
               'shipping_id'   => $order["shipping_id"],
               'shipping_name' => $this->shippings[$order["shipping_id"]],
            );
        }
		
        if($order['payment_id']){
            $pay_mod = &m("payment");
            $aOrder["payment"] = $pay_mod->get("ismobile=0 AND payment_id='{$order["payment_id"]}'");
        }
        
        if($order['address']){
            
            $mAddr = &m('address');
            
            $aOrder['address'] = $mAddr->get("addr_id='{$order['address']}' AND user_id = '{$this->visitor->get("user_id")}'");
        }
        
        $aOrder["user"] = array(
            "user_id"    => $this->visitor->get("user_id"),
            'user_name'  => $this->visitor->get("user_name"),
        );    
        return $aOrder;
    }
	
	
	/**
	 * 获得当前用户的余额
	 *
	 * @param NULL
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	private function _memberSurplus(){
        $member_mod = &m("member");
        $mInfo = $member_mod->get($this->visitor->get("user_id"));
        return $mInfo["money"];
    }
	
	 /**
     *    确认订单
     *
     *    @author    Garbin
     *    @return    void
     */
	function fabricop(){
          $order_id  = isset($_GET['order_id'])  ? intval($_GET['order_id'])  : 0;
          $opt       = isset($_GET['opt'])       ? trim($_GET['opt'])  : '';
   
          $user_id = $this->visitor->get('user_id');
          
          $order_mod = m('order');
          
          $info = $order_mod->get("order_id='{$order_id}' AND user_id = '{$user_id}' AND extension = 'fabricbook'");
          
          $aData = array();
          
          if($opt == "finished" && $info['status'] == "30"){
              $aData = array(
                  "finished_time" => gmtime(),
                  'status'        => 40,
              );
          }
          
          if($opt == "cancel" && $info['status'] == "11"){
              $aData = array(
                  "last_modified" => gmtime(),
                  'status'        => 0,
              );
          }

          if($opt == "return" && $info['status'] == "40"){
              $aData = array(
                  "last_modified" => gmtime(),
                  'status'        => 70,
              );
          }
          
          if($opt == "cancelrefund" && $info['status'] == "50"){
              $aData = array(
                  "last_modified" => gmtime(),
                  'status'        => 60,
              );
              
              $refund_mod = m("bookrefund");
              $refund_mod->edit("order_id='{$order_id}' AND user_id = '{$user_info['user_id']}' AND status=0",  array("status" => 2));
          }
          
          
          $res = $order_mod->edit($order_id, $aData);
          
      	   if($res){
				header('Location:fabricbook-applyRecord.html');
	       } else {
			   $this->show_warning('意外错误！');
				return;
	      }
	}
      
   
}

?>
