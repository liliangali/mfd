<?php
use Cyteam\Shop\Type;
use Cyteam\Shop\Type\Types;
/**
 * 新版普通订单
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: news.otype.php 14280 2016-03-21 08:26:11Z lil $
 * @copyright Copyright 2015 redcollar
 */
class NewsOrder extends BaseOrder
{
    var $_name = 'normal';
    public $mRe;
    public $mCf;
    
    function __construct(){
		parent::__construct();
		$this->mRe = &m('region');
		$this->mCf  = &m('customer_figure');
		$this->_newpromotion_mod =& m('newpromotion');
		import('shopConf');
		import('shopCommon');
    }
    
    public function verifyOrderData($input){
        $mCart = &m('cart');
        $cart  = $mCart->_cart_check(ShopCommon::user_id(),ShopCommon::cartCookieGet('check'));

        if ($cart['check_num'] <= 0) {
            $this->_error('请选择要购买的商品');
            return false;
        }
   
        $checkStock = ShopCommon::checkFabricStock(current($cart['stock']));
   
        if ($checkStock !== true){
            $this->_error($checkStock.'库存不足');
            return false;
        }
        
        if ($cart['has_nosale'] || $cart['has_nofabric']){
            $this->_error('已下架商品不能买，请返回购物车重新选择。');
            return false;
        }
        ## 校验库存
        /* $stock = Carts::_cart_stock($cart['object']);
        if ($stock !== true){
            self::$_error == $stock;
            return false;
        } */
        
        
        
        
        $order = (array)$_SESSION['_order'];
     
        
        $order['user'] = ShopCommon::userInfo();
        
        ## 合并量体数据 + 计算量体费
        ShopCommon::figureVip($cart,$order['user']);
        
        ## 校验收货信息
        $order['shipping'] = $this->checkShip($input['order']['ship'],$cart);
        if ($order['shipping'] === false)
            return false;
        
        ## 校验发票
        $order['invoice'] = $this->checkTax($input['order']['invoice']);
        if ($order['invoice'] === false)
            return false;
        
        ## 校验支付方式
        $order['payment'] = $this->checkPay($input['order']['payment']);
        
        ## 校验酷卡
        $order['kuka'] = $this->checkKuka($input['dedu'],$cart);
        
        ## 校验余额/麦富迪币
        $order['dedu'] = $this->checkDedu($input['dedu'],$cart,$order['user']);
        
        ## 校验支付密码
        if ($order['dedu']){
            $ckPayPwd = $this->checkPayPwd($order['dedu'], $input, $order['user']);
            if ($ckPayPwd === false){
                return false;
            }
        }
        
        ## 校验抵扣券
        $order['debit'] = $this->checkDebit($input['dedu'],$cart,$order['user']);

        ## 校验订单备注
        $order['remark'] = isset($input['order']['remark']) ? stripslashes(trim($input['order']['remark'])) : '';
        
        ## 结算统计
        $order['cart'] = ShopCommon::totalData($order,$cart);
        
        return $order;
    }
    
    /**
     * 校验收货地址
     *
     * @date 2015年12月7日 上午9:48:26
     *
     * @author Ruesin
     */
    function checkShip($ship,$cart = array()){
        
        if(!isset($ship['type']) || !isset(ShopConf::$shippingWays[$ship['type']]) ){ 
            $this->_error('请选择配送方式');
            return false;
        }
        
        if ($ship['type'] == 1){
            
            $mAddress = &m('address');
            $address = $mAddress->get("addr_id = '{$ship['address']}' AND user_id = ".ShopCommon::user_id());
            
            if (!$address){
                $this->_error('收货地址有变更，请重新选择！');
                return false;
            }
            
            $delivery_time = (!isset($ship['delivery_time']) || !isset(ShopConf::$deliveryTime[$ship['delivery_time']]) ) ? 1 : $ship['delivery_time'];
            
            $ship_mob  = $address['phone_mob'];
            $ship_name = $address['consignee'];
        }else{
        
            $mServe = &m('serve');
            $servers = $mServe->get("region_id = '{$ship['region_id']}' AND virtual = '0' AND shop_type IN (1,2) AND idserve= '{$ship['server_id']}'");
            if (!$servers){
                $this->_error('自提门店信息有误，请重新选择！');
                return false;
            }
            
            $delivery_time = 1;
            
            if(!trim($ship['ship_name'])){
                $this->_error('自提人姓名错误!');
                return false;
            }
            
            if(!preg_match("/^1[34578]\d{9}$/", $ship['ship_mob'])){
                $this->_error('自提人手机号错误!');
                return false;
            }
            
            $ship_mob  = $ship['ship_mob'];
            $ship_name = $ship['ship_name'];
        }
        
        if(strlen($ship_name) > 18){
            $this->_error('收货人姓名超长!');
            return false;
        }
        if (!isset($cart['weight']) || empty($cart['weight'])){
            $this->_error('请确认商品重量!');
            return false;
        }
        
        //物流公司、运费计算
        if (!isset($ship['shipsId'])){
            $this->_error('请选择物流公司!');
            return false;
        }
       
        imports("orders.lib");
        $orderLib = new Orders();
        $shipInit = $orderLib->getShipInit(['ship_area_id'=>$address['region_id'],'weight'=>$cart['weight']],$ship['shipsId']);
        
        if (!$shipInit){
            $this->_error('缺少物流公司!');
            return false;
        }
        
        //商品促销 存在免邮
        if (isset($cart['free_shipping']) && !empty($cart['free_shipping'])){
            $shipInit['defship']['post_fee'] = '0.00';
        }

        return array(
                'type'         => $order['shipping']['type'],
                'id'           => ShopConf::$shippingWays[$ship['type']]['id'],
                'shipping'     => ShopConf::$shippingWays[$ship['type']]['name'],
                'ship_name'    => $ship_name,
                'ship_mobile'  => $ship_mob,
                'address'      => isset($address) ? $address : array(),
                'store'        => array('server_id'=>isset($servers)?$servers['idserve']:0),
                'delivery_time' => $delivery_time,
                'defship' => $shipInit['defship'],
        );
    }
    
    /**
     * 校验发票
     *
     * @date 2015年12月7日 上午9:48:44
     *
     * @author Ruesin
     */
    function checkTax($invoice){
        
        $type = isset($invoice['id']) ? intval($invoice['id']) : 0;
        
        if(!$type || !isset(ShopConf::$invoiceTypes[$type])){
            return array(
                    'need' => 0,
                    'type' => '',
                    'title'=> '',
                    'com'  => '',
            );
        }
        
        $data = $invoice[$type];
         
        if ($type == 3){
            if (count($data) !== count(ShopConf::$invoiceFields)){
                $this->_error('发票信息不完整');
                return false;
            }
            foreach (ShopConf::$invoiceFields as $k=>$v){
                if (!isset($data[$k]) || !$data[$k]){
                    $this->_error('请正确填写'.$v);
                    return false;
                }
            }
            
        }
        
        $title = ($type == 3) ? json_encode($data,JSON_UNESCAPED_UNICODE) : $data['title'];
        
        return array(
                'need' => 1,
                'type' => ShopConf::$invoiceTypes[$type],
                'title'=> $title,
                'com'  => $type,
        );
    }
    
    /**
     * 校验支付方式
     *
     * @date 2015年12月7日 上午10:01:42
     *
     * @author Ruesin
     */
    function checkPay($pay){
        
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
        
        return array(
                'payment_id'   => $payment['payment_id'],
                'payment_code' => $payment['payment_code'],
                'payment_name' => $payment['payment_name']
        );
        
    }
    
    /**
     * 校验酷卡
     *
     * @date 2015年12月7日 上午10:14:40
     *
     * @author Ruesin
     */
    function checkKuka($dedu,$cart){
        
        $return = array();
        
        if (empty($cart['cloth_ka']) || !isset($dedu['kuka']) || !isset($dedu['kuka']['use']) || empty($dedu['kuka']['use'])){
            return $return;
        }
        
        $kukas = ShopCommon::kukas('sn');
        
        foreach ($dedu['kuka']['use'] as $key=>$val){
            if (isset($kukas[$key]) && intval($val) > 0){
                //$return[$kukas[$key]['id']] = $kukas[$key]['work_num'];
                $return[$kukas[$key]['id']] = $kukas[$key];
            }
        }
        return $return;
    }
    
    /**
     * 校验酷币/余额
     * 
     * 包含可使用酷币的正常款，所有角色可用。
     * 没有可使用酷币的正常款，创业者可用。
     * 
     * 使用麦富迪币|余额，多于会员本身拥有的麦富迪币|余额，重置。
     *  
     * 会员等级为1时，如果可以使用麦富迪币的正常款为空，清空麦富迪币的使用。
     *
     * @date 2015年12月7日 上午10:32:06
     *
     * @author Ruesin
     */
    function checkDedu($dedu,$cart,$user){
        
        $coin  = (isset($dedu['coin']) && intval($dedu['coin']) >0) ? intval($dedu['coin']) : 0;
        $money = (isset($dedu['money']) && intval($dedu['money']) >0) ? intval($dedu['money']) : 0;

        $return = ['coin'=>$coin,'money'=>$money];
        if ($coin > $user['coin']){
            $return['coin'] = $user['coin'];
        }
        
        if($money > $user['money']){
            $return['money'] = $user['coin'];
        }
        
        if ($user['member_lv_id'] == 1){
            if (empty($cart['cloth_bi'])){
                $return['coin'] = 0;
            }
        }else{
            
        }
        
        return $return;
    }
    
    /**
     * 校验支付密码
     *
     * @date 2015年12月7日 上午10:40:15
     *
     * @author Ruesin
     */
    function checkPayPwd($dedu,$input,$user){
        
        $pwd  = isset($input['dedu']['payPwd']) ? trim($input['dedu']['payPwd']) : '';
        
        if ($dedu['coin'] > 0 || $dedu['money'] > 0){
            if (!$pwd){
                $this->_error('请输入支付密码!');
                return false;
            }
            if (!$user['pay_password']){
                $this->_error('请先设置支付密码！');
                return false;
            }
            
            if ($user['pay_password'] != md5($pwd)) {
                $this->_error('支付密码错误！');
                return false;
            }
            
        }
        return true;
    }
    
    /**
     * 校验优惠券
     * 
     * 创业者：不能使用抵用券
     * 酷币、优惠券不能同时使用，酷币优先级高于优惠券。[已废弃此限制,PM邮件确认]
     * 可使用优惠券的正常款，为空；或者通过正常款查出的可使用的券，不存在，清空。
     *
     * @date 2015年12月7日 上午10:44:01
     *
     * @author Ruesin
     */
    function checkDebit($dedu,$cart,$user){
        
        //if (intval($order['dedu']['coin']) > 0){
        //    return [];
        //}
        if (!isset($dedu['debit']) || !isset($dedu['debit']['sn']) || $dedu['debit']['sn'] == ''){
            return array();
        }
        
//        if($user['member_lv_id'] > 1){
//            return array();
//        }
        
        if (empty($cart['cloth_quan'])){
            return array();
        }
//      echo '<pre>';print_r($cart);exit;
        
        $debits = ShopCommon::debits($cart,'id');
//        echo '<pre>';print_r($debits[$dedu['debit']['sn']]);exit;
        if(!isset($debits[$dedu['debit']['sn']])){
            return array();
        }
        $d = $debits[$dedu['debit']['sn']];

        $diy_price = $cart['diy_price'];
        $custom_price = $cart['custom_price'];

        //=====  券种类  =====
        $money = $d['money'];
        if($d['category_r'] == '1')
        {
            if($diy_price < $money)
            {
                $d['money'] = $diy_price;
            }
        }
        elseif ($d['category_r'] == '2')
        {
            if($custom_price < $money)
            {
                $d['money'] = $custom_price;
            }
        }

        return $d;
    }
    
    
        
    /**
     * 提交订单
     * 
     * @date 2015-9-16 下午2:06:45
     * @author Ruesin
     */
    function submit($data) {
    
    	$baseinfo = $this->_handle_order_info($data);
    	
    	if(!$baseinfo){
    	    $this->_error('订单提交失败！错误码：0010010100');
    	    return false;
    	}
    	$order_id = $this->_mod_order->add($baseinfo);
    	if (!$order_id){
    		$this->_error('订单提交失败！错误码：0010010101');
    		return false;
    	}

    	$goods = $this->_format_order_goods($data['cart']['object']);
   
    	$customs = ShopConf::_customs();
    	
    	
    	$stock = [];
    	$measure = [];
    	$hasPrice = $mtmN = 0;
    	$countGoods = count($goods['goods']);
    	
    	$mtmPrice = ($data['cart']['final_amount'] * 1) + ($data['cart']['money_amount'] * 1) + ($data['cart']['coin']*1) + ($data['cart']['kuka_fee'] * 0.5);
    
    	foreach ($goods['goods'] as $key => $value)
    	{
    	    $price = $mtmPrice*($value['subtotal']/$goods['amount']);  // 给MTM推送的价格 订单最终支付价格 * (小计/商品总价)
    	    $value['goods_sn'] = $this->_gen_goods_sn();
    	    if($value['favorable_id']){
    	        $newpro_fav=$this->_newpromotion_mod->get(array(
    	        'conditions'=>"id='{$value['favorable_id']}'",
    	    ));
    	        if($newpro_fav['name']){
    	            $favname=$newpro_fav['name'];
    	        }else{
    	            $favname='';
    	        }
    	    }else{
    	            $favname='';
    	        }
    	        if($value['zhek_id']){
    	            $newpro_zk=$this->_newpromotion_mod->get(array(
    	                'conditions'=>"id='{$value['zhek_id']}'",
    	            ));
    	            if($newpro_zk['name']){
    	                $zkvname=$newpro_zk['name'];
    	            }else{
    	                $zkvname='';
    	            }
    	        }else{
    	            $zkvname='';
    	        }
    
    	    $goods_items[] = array(
    	    
    	            'order_id'      => $order_id,
    	            'son_sn'        => '',
    	            'goods_sn'      => $value['goods_sn'],
    	            'goods_id'      => $value['params']['oProducts']['product_id'], //货品id
    	            'goods_name'    => $value['goods']['name'],
    	            'goods_image'   => $value['goods']['image'] ? $value['goods']['image'] : '',
    	            
    	            //为了价格统一  使用比率计算出来的价格
    	            'price'         => $value['newPrice']['price'],  //订单单品显示价格  为各种折扣之后的最终价格 总和为订单最终价格
    	            'markprice'     => isset($value['newPrice']['markprice']) ? $value['newPrice']['markprice'] : $value['newPrice']['price'],  //吊牌价 为不打折之前的市场价
    	            'mtm_price'     => (++$mtmN == $countGoods) ? intval($mtmPrice-$hasPrice) : intval($price), //MTM推送的价格 总和为订单最终支付价格 方便财务对账
    	            'quantity'      => $value['quantity'],
    	            'subtotal'      => $value['newPrice']['subtotal'], //
    	             
    	            //初始价格 不管是否为活动
    	            'nprice'         => $value['goods']['nPrice'],
    	            'nmarkprice'     => isset($value['goods']['nMarkprice']) ? $value['goods']['nMarkprice'] : $value['goods']['nPrice'],
    	             
    	            //订单流程中的价格 有可能会被重置为活动价
    	            'oprice'         => $value['goods']['price'],
    	            'omarkprice'     => isset($value['goods']['markprice']) ? $value['goods']['markprice'] : $value['goods']['price'],
    	            
    	            'source_id'     => isset($value['source_id']) ? $value['source_id'] : '',
    	            'type' 		    => $value['type'],
    	            
    	            'cloth'         =>  $value['cloth'],   //品类
    	
    	            'dis_ident'     => $value['dis_ident'], //套装标识
    	            'suit_id'       => $value['suit_id'],  //套装ID
    	
    	            
    	            'fabric'		=> $value['fabric'],
    	            'lining'        => $value['lining'],
    	            'button'        => $value['button'],
    	            'style'         => $value['style'],
    	            'embs'		    => is_array($value['embs']) ? json_encode($value['embs'],JSON_UNESCAPED_UNICODE) : $value['embs'],
    	            'syline'        => $value['syline'],
    	            'params'        => is_array($value['params']) ? json_encode($value['params'],JSON_UNESCAPED_UNICODE) : $value['params'],
    	            'items'         => is_array($value['items']) ? json_encode($value['items'],JSON_UNESCAPED_UNICODE) : $value['items'],
    	            'size'          => $value['size'],
    	            'first'         => $value['first'],
    	             
    	            'activity'      => isset($value['promotion']['id']) ? $value['promotion']['id'] : 0,
    	            
    	            'crafts'        => isset($value['promotion']['id']) ? json_encode($value['promotion'],JSON_UNESCAPED_UNICODE) : '', //促销信息

                'dog_name'         => $value['dog_name'],
                'dog_date'         => $value['dog_date'],
                'dog_desc'         => $value['dog_desc'],

                'body_condition'         => $value['body_condition'],
                'run_time'         => $value['run_time'],
                'weight'         => $value['weight'],
                'time_id'         => $value['time_id'],
                'dog_nums'         => $value['dog_nums'],
    	        'fav_id'         =>$value['favorable_id'],
    	        'fav_name'      =>$favname,
    	        'zhek_id'         =>$value['zhek_id'],
    	        'zhek_name'      =>$zkvname,
    	        
    	        'fav_price'      =>$value['favprice'],
    	            
    	    );

    	    $hasPrice += intval($price);
    	    unset($price);
    	   

    	    
    	    ## 需要减库存
    	  
//    	    $pInfo = $value['params']['oProducts'];
//    	    if ($value['type'] != 'fdiy')
//    	    {
//    	        $stock[$pInfo['product_id']] = isset($stock[$pInfo['product_id']]) ? $stock[$pInfo['product_id']] + $value['quantity'] : $value['quantity'];
//
//    	        //购买数量
//    	        $purchase[$pInfo['goods_id']] = isset($purchase[$pInfo['goods_id']]) ? $purchase[$pInfo['goods_id']] + $value['quantity'] : $value['quantity'];
//    	    }
    	   
    	}
    	
    	if (isset($data['cart']['figure']) && !empty($data['cart']['figure'])){
    	    $figures = $this->_get_figures($data['cart']['figure']);
    	    if ($figures == false){
    	        return false;
    	    }
    	    $ofData = $fomData = $mfData =array();
    	    
    	    $fom  = &m('figureorderm');
    	    $mCf  = &m('customer_figure');
    	    
            
            // # 顾客表
            if (isset($figures['customer']) && !empty($figures['customer'])) {
                foreach ($figures['customer'] as $k => $v) {
                    $mfData[] = $v;
                }
            }
            
            
    	}
    	
    	$res = $this->_mod_ordergoods->add(addslashes_deep($goods_items));
    	 
    	if (!$res){
    	    $this->_error('订单提交失败！错误码：003003101');
    	    return false;
    	}
    	
    	
    	//减库存
    	$mFabirc = &m('products');
    	
    	foreach ($stock as $key=>$val){
    	    $res = $mFabirc->setDec(" product_id = '{$key}'",'store',$val);
    	    if($res <= 0){
    	        $this->_error('订单提交失败！错误码：004003101'.$key);
    	        return false;
    	    }
    	}
    	
    	//增加购买数量
    	$this->_mod_goods  = &m('goods');
    	foreach ((array)$purchase as $key=>$v){
    	    $this->_mod_goods->update_goods_count($key,$v);
    	}
//        echo '<pre>';print_r($data);exit;
    	//抵扣券
    	if(isset($data['debit']) && !empty($data['debit']) && $data['cart']['debit_fee'] > 0){
    	    
    	    $debtis = array(
    	            'order_id' => $order_id,
    	            'd_id'     => $data['debit']['id'],
    	            'd_sn'     => $data['debit']['code'],
    	            'd_name'   => $data['debit']['name'],
    	            'd_money'  => $data['debit']['money'],
    	            'd_cloth'  => implode(',', $data['debit']['category']),
    	    );
    	    $mOdebit = &m('orderdebit');
    	    $res = $mOdebit->add(addslashes_deep($debtis));
    	    
    	    if (!$res){
    	        $this->_error('订单提交失败！错误码：004004101');
    	        return false;
    	    }
//    	    $mDebit = &m('debit');
            $mDebit = &m('voucher');

            
    	    $res = $mDebit->edit("id = '{$data['debit']['id']}'",array('use_status'=>1,'order_id'=>$baseinfo['order_sn']));
    	    if($res <= 0){
    	        $this->_error('订单提交失败！错误码：004004102');
    	        return false;
    	    }
    	}
    	
    	//处理余额 麦富迪币
    	$cashLog = $upUser = array();
    	
    	// 订单日志
    	$orderLogs[] = [
    	        'order_id' => $order_id,
    	        'op_id' => $data['user']['user_id'],
    	        'op_name' => $data['user']['user_name'],
    	        'behavior' => 'create',
    	        'from' => '',
    	        'to' => ORDER_PENDING,
    	];
    	
    	if ($baseinfo['status'] != ORDER_PENDING) {
    	   if ($baseinfo['status'] == ORDER_ACCEPTED){
//    	        $orderLogs[] = [
//    	                'order_id' => $order_id,
//    	                'op_id'    => $data['user']['user_id'],
//    	                'op_name'  => $data['user']['user_name'],
//    	                'behavior' => 'payment',
//    	                'from'     => ORDER_PENDING,
//    	                'to'       => ORDER_ACCEPTED,
//    	                'text'     => '订单创建并支付成功!'
//    	        ];
//               echo '<pre>';print_r($baseinfo);exit;
               $baseinfo['order_id'] = $order_id;
                $this->lastSomeThing($baseinfo);
    	    }
    	
//    	    //写财务报表
//    	    $res = ShopCommon::recordFinance($baseinfo);
//    	    if ($res === false){
//    	        $this->_error('订单提交失败！错误码：004005103');
//    	        return false;
//    	    }
//
//    	    /* 订单满额 送券 接口  $amount : 订单总额  $user_id:user_id */
//    	    give_debit($baseinfo['final_amount'],$baseinfo['user_id']);
    	    
    	}
//    	if(ShopCommon::recordLogs($orderLogs) !== true){
//    	    self::$_error = '订单提交失败！错误码：004005101';
//    	    return false;
//    	}
    	

    	if($baseinfo['status'] == ORDER_ACCEPTED){
    	    
    	    $res = $this->_mod_orderpccron->_addToList($order_id);
    	    if ($res === false){
    	        $this->_error('订单提交失败！错误码：004005102');
    	        return false;
    	    }
    	}elseif ($baseinfo['status'] == ORDER_WAITFIGURE){
    	    
    	}
    	
    	return array('order_id' => $order_id, 'order_sn' => $baseinfo['order_sn'],'status' => $baseinfo['status']);
    }

    /**
     * 支付成功后各种操作
     *
     * @date 2015-10-30 上午9:28:30
     * @author Ruesin
     */
    public function lastSomeThing($order_info){


        $orderLogs[] = [
            'order_id' => $order_info['order_id'],
            'op_id'    => $order_info['user_id'],
            'op_name'  => $order_info['user_name'],
            'from'     => $order_info['status'],
            'to'       => $order_info['newStatus'],
            'behavior' => 'payment',
            'remark'   => '在线支付成功，支付单号为:'.$order_info['out_trade_sn'],
        ];
        ShopCommon::recordLogs($orderLogs);
        ShopCommon::recordFinance($order_info);
        ShopCommon::give_debit($order_info['final_amount'],$order_info['user_id']);
        ShopCommon::f_goods($order_info);

        //===== 执行成功 推送mes       =====
        $goods = Types::createObj("fdiy");
        $res = $goods->mesf($order_info['order_id']);
        /* 订单满额 送券 接口  $amount : 订单总额  $user_id:user_id */
//        give_debit($order_info['final_amount'],$order_info['user_id']);



    }

    
    /**
     * 获取订单量体信息
     * 
     * 一个订单根据顾客（手机号+姓名）对应多条量体数据，一条量体数据对应多个商品。
     * 
     * @date 2015-10-8 下午3:25:31
     * @author Ruesin
     */
    function _get_figures($figures){
        
        $figureFileds = ShopConf::figureFields();
        
        $result = array();
        foreach ($figures as $sRow){
            $sRow['son_sn'] = $this->_gen_son_sn();
            $figure = $mfData = array();
            
            if( $sRow['type_id'] == 1 || $sRow['type_id'] == 2  || $sRow['type_id'] == 6){
                
                $figure = array(
                        'son_sn'    => $sRow['son_sn'],
                        'measure'   => $sRow['type_id'],
                        'realname'  => $sRow['realname'],
                        'phone'     => $sRow['phone'],
                        'gender'    => isset($sRow['gender']) ? $sRow['gender'] : 10040,
                        'area'      => $sRow['region_name'],
                        'addr'      => isset($sRow['addr']) ? $sRow['addr'] : '',
                        'time'      => $sRow['time'],
                        'time_noon' => $sRow['time_noon'],
                        'server_id' => $sRow['server_id'],
                        'userid'    => ShopCommon::user_id(),//$sRow['user_id'],  // 好像不对
                        'figure_state' => 0,
                        'liangti_id'   => isset($sRow['liangti']) ? intval($sRow['liangti']) : 0,
                        'liangti_name' => isset($sRow['liangti_name']) ? $sRow['liangti_name'] : '',
                );
                 
                //创业者顾客表数据
                $mfData = array(
                        'son_sn'          => $sRow['son_sn'],
                        'figure_state'    => '0',
                        'storeid'         => ShopCommon::user_id(),//$sRow['user_id'],  // 好像不对
                        'customer_name'   => $sRow['realname'],
                        'customer_mobile' => $sRow['phone'],
                        'firsttime'       => time(),
                        'lasttime'        => time(),
                        'gender'          => isset($sRow['gender']) ? $sRow['gender'] : 10040,
                        'service_mode'    => $sRow['type_id'],
                        'id_serve'        => $sRow['server_id'], //.
                        //'liangti_id'   => isset($sRow['liangti']) ? intval($sRow['liangti']) : 0,
                        //'liangti_name' => isset($sRow['liangti_name']) ? $sRow['liangti_name'] : '',
                );
                
            }else if($sRow['type_id'] == 5){

                /*if (!isset($history[$sRow['history_id']])){
                    $this->_error('已有量体数据有误！');
                    return false;
                }*/
                $hRow = $sRow['historyInfo'];
                
                $figure  = array(
                        'son_sn'        => $sRow['son_sn'],
                        'measure'       => 5, // $sRow['type_id'],
                        'realname'      => $hRow['customer_name'],
                        'phone'         => $hRow['customer_mobile'],
                        'gender'        => $hRow['gender'],
                        'liangti_id'    => $hRow['liangti_id'],
                        'liangti_name'  => $hRow['liangti_name'],
                        'liangti_state' => 1,
                        'history_id'    => $hRow['figure_sn'],
                        // 'area' => $history['region_name'],
                        // 'addr' => $history['addr'],
                        // 'time' => $history['time'],
                        // 'time_noon' => $history['time_noon'],
                        'server_id'      => $hRow['id_serve'],
                        'userid'         => ShopCommon::user_id(),// $sRow['user_id']
                        'figure_state'   => $hRow['figure_state'],
                        'modi_time'      => $hRow['lasttime'],
                );
                
                foreach ($figureFileds as $key => $row) {
                    if (isset($row['isshow']) && $row['isshow'] == 1) {
                
                        if ($key == 'lheight' || $key == 'lweight') {
                            $hRow[$key] = $hRow[str_replace('l', '',$key)];
                        }
                        if (intval($hRow[$key]) <= 0) {
                            $this->_error((isset($row['zname']) ? $row['zname'] : '') .' - 已有量体数据有误!');
                            return false;
                        }
                    }
                    $figure[$key] = $hRow[$key];
                }
            }
            
            if (!empty($mfData))
                $result['customer'][] = $mfData;
            
            $result['figure'][] = $figure;
            
            foreach ($sRow['ident'] as $v) {
                $result['goods'][$v . ''] = $sRow['son_sn'];
            }
            
            $mfData = $figure = array();
        }
        
        return $result;
    }
    
    
    /**
     * 格式化要入库到order_goods表的数据
     *
     * 价格不能按照订单流程中的，要按比例进行重新计算，好给财务对账。Orz..
     * 
     * 吊牌价：市场价。
     * 结算价：折扣之后的价格。
     * 订单总和：各个小计的总和。
     * 
     * 样衣的价格，不能取样衣的表中的价格，得按照套装的价格进行按比率运算。
     * @author Ruesin
     */
    function _format_order_goods($objs = array()){
 
        foreach ($objs as $oKey=>$oRow){
            foreach ($oRow['list'] as $ok=>$oArr){
                if ($oArr['check']){
                    if ($oRow['type'] == 'suit'){
                        ## 套装包含的样衣价格总和
                        $prices[$oKey] = ( isset($prices[$oKey]) && intval($prices[$oKey])>0 ) ? ($prices[$oKey]+$oArr['goods']['price']) : $oArr['goods']['price'];
                    }else{
                        ## diy价格
                        $prices[$ok] = $oArr['goods']['price']+0;
                    }
                }
            }
        }
        
        $goods = $suit = array();
        $goodsAmount = 0;
        
        foreach ($objs as $key=>$row){
            
            $countList = 0;
            $countList = count($row['list']);
            
            $prcN = $hasMkt = $mktPrice = $hasPrc = $price = 0;
            foreach ($row['list'] as $k=>$a){
                if ($a['check']){
               
                    if ($row['type'] == 'suit'){
                    
                        $prcN += 1; //prcN 次数 用来判断是不是套装内的最后一个, end() 感觉不严谨
                    
                        
                        if (isset($row['goods']['qqsfzdml']) && $row['goods']['qqsfzdml']){
                            $mktPrice = $row['goods']['markprice']*($a['goods']['price']/$prices[$key]); // 吊牌价  套装市场价 * (样衣价格/套装内样衣价格总和) = 套装市场价 * 样衣价格在套装所有样衣价格中的比例
                        }else{
                            $mktPrice = $row['goods']['nMarkprice']*($a['goods']['price']/$prices[$key]); // 吊牌价  套装市场价 * (样衣价格/套装内样衣价格总和) = 套装市场价 * 样衣价格在套装所有样衣价格中的比例
                        }
                        if($a['goods']['favprice']){
                            $a['favpriceaft']=$a['goods']['subtotal']-$a['goods']['favprice'];
                        }
                        
                        $price    = $row['goods']['price']*($a['goods']['price']/$prices[$key]);      // 销售价 套装销售价 * (样衣价格/套装内样衣价格总和) = 套装销售 * 样衣价格在套装所有样衣价格中的比例
                    
                        
                        if (isset($row['goods']['qqsfzdml']) && $row['goods']['qqsfzdml']){
                            $a['newPrice']['markprice'] = ( $prcN == $countList) ? $row['goods']['markprice'] - $hasMkt : intval($mktPrice); //如果是最后一个 就取剩下的所有 否则就用按比例算出来的
                        }else{
                            $a['newPrice']['markprice'] = ( $prcN == $countList) ? $row['goods']['nMarkprice'] - $hasMkt : intval($mktPrice); //如果是最后一个 就取剩下的所有 否则就用按比例算出来的
                        }
                        
                        
                   
                        $a['newPrice']['price']     = ( $prcN == $countList) ? $row['goods']['price'] - $hasPrc : intval($price); //如果是最后一个 就取剩下的所有 否则就用按比例算出来的
                        $a['newPrice']['subtotal']  = $a['newPrice']['price']*$a['quantity'];  // 小计 销售价 * 数量
                    
                        $goods[$a['ident']] = $a;
                    
                        $row['activity'] = isset($a['activity']) ? $a['activity'] : 0 ;
                    
                        $goodsAmount += $a['subtotal'];
                    
                        $hasMkt += intval($mktPrice);
                        $hasPrc += intval($price);
                    
                    }else{
                        $a['newPrice']['markprice'] = intval($a['goods']['nMarkprice']);  //未做任何折扣之前的市场价
                        $a['newPrice']['price']     = intval($a['goods']['price']);  // 销售价
                        $a['newPrice']['subtotal']  = $a['newPrice']['price']*$a['quantity'];

                          $a['favprice']= $row['subtotal']-$row['favprice'];
                       
                        $goods[$k] = $a;
                    
                        $goodsAmount += $a['subtotal'];
                    }
                }
            }
            
            
            if($row['type'] == 'suit' && $row['check']){
                $suit[] = array(
                        'goods_id'    => $row['goods']['id'],
                        'goods_name'  => $row['goods']['name'],
                        'goods_image' => $row['goods']['image'],
                        'price'       => $row['goods']['price'],
                        'subtotal'    => $row['subtotal'],
                        'quantity'    => $row['quantity'],
                        'type'        => $row['type'],
                        'dis_ident'   => $row['ident'],
                        'activity'    => $row['activity'] ? $row['activity'] : 0,
                );
            }
            
        }

        return array(
                'goods'  => $goods,
                'suit'   => $suit,
                'amount' => $goodsAmount,
        );
    }
    
    
    /**
     * 处理订单基本信息
     *
     * @author Ruesin
     */
    function _handle_order_info($order)
    {
 

        if($order['cart']['final_amount'] <= 0){
            if($order['cart']['has_measure'] == 1){
                $order_status = ORDER_WAITFIGURE;   // 待量体
            }else{
                $order_status = ORDER_ACCEPTED; //已付款
            }
        }else{
            $order_status = ORDER_PENDING; //待付款
        }


        return array(
                
                'order_sn'       => $this->_gen_order_sn(),
                'extension'      => 'news',
                'discount'       => $order['cart']['discount'],
                'goods_amount'   => $order['cart']['goods_amount_m'],
                'final_amount'   => $order['cart']['final_amount']+$order['shipping']['defship']['post_fee'],
                'order_amount'   => $order['cart']['order_amount']+$order['shipping']['defship']['post_fee'],
                'money_amount'   => $order['cart']['money_amount'],
                'profit_amount'  => isset($order['cart']['profit_amount']) ? $order['cart']['profit_amount'] : 0,
                'coin'           => $order['cart']['coin'],
                'point_g'        => $order['cart']['point_g'],
                
                'user_id'        => $order['user']['user_id'],
                'user_name'      => $order['user']['user_name'],
                'channel_pid'      => $order['user']['channel_pid'],
                'bd_dis_count'   => $order['cart']['bd_dis_count'],
                'cy_dis_count'   => $order['cart']['cy_dis_count'],
                
                'has_measure'    => $order['cart']['has_measure'],
                
            
                'one_num'        => '',
                'one_id'         => '',
                'one_status'     => 1,
        
                'status'         => $order_status,
                'add_time'       => time(),
                //'finished_time'  => '',
                'last_modified' => time(),
        
                //是否上门量体
                'measure'     => '',
               
            
                'is_gift'     => 0,
        
                //支付
                'payment_id'     => $order['payment']['payment_id'],
                'payment_name'   => $order['payment']['payment_name'],
                'payment_code'   => $order['payment']['payment_code'],
                //'out_trade_sn'   => '',
                //'pay_time' => '',
                //'pay_message' => '',
                //'cost_payment'  => $order['cart']['final_amount'],  //订单支付金额
        
                
                //收货信息
                'ship_area'      => isset($order['shipping']['address']['region_name']) ? $order['shipping']['address']['region_name'] : '',
                'ship_area_id'   => isset($order['shipping']['address']['region_id']) ? $order['shipping']['address']['region_id'] : '',
                'ship_addr'      => isset($order['shipping']['address']['address']) ? $order['shipping']['address']['address'] : '',
                'ship_name'      => $order['shipping']['ship_name'],
                'ship_zip'       => isset($order['shipping']['address']['zipcode']) ? $order['shipping']['address']['zipcode'] : '',
                'ship_mobile'    => $order['shipping']['ship_mobile'],
                'ship_tel'       => isset($order['shipping']['address']['phone_tel']) ? $order['shipping']['address']['phone_tel'] : '',
                'ship_email'     => isset($order['shipping']['address']['email']) ? $order['shipping']['address']['email'] : '',
                'delivery_time'  => $order['shipping']['delivery_time'],
                
                //配送
                'shipping_id'    => $order['shipping']['defship']['shipping_id'], //物流公司id
                'measure_fee'    => $order['shipping']['defship']['post_fee'],    //物流运费
                'shipping'       => $order['shipping']['shipping'],
                'ship_store'     => $order['shipping']['store']['server_id'],//自提门店
        
                //发票
                'invoice_need'   => $order['invoice']['need'],
                'invoice_type'   => $order['invoice']['type'],
                'invoice_title'  => $order['invoice']['title'],
                'invoice_com'    => $order['invoice']['com'],
        
                'memo'          => $order['remark'],
                //'ip'            => $order['source']['ip'] ,
                'source_from'   => 'pc',
        );
        
    }
    
    function _get_figure_data($input,$order){
        
        
    }
    
    function _get_figure_datas($input,$order){
        if(!$input['figure']){
            return [];
        }
        $type = $input['figure']['type'];
        $data = $input['figure']['data'][$type];
        if( $type == 1 || $type == 2  || $type == 6){
            //取地区先放在这里  其实是可以放在操作过程中的
            $figure = array(
                    'goods_sn'  => $input['goods_sn'],
                    'measure'   => $type,
                    'realname'  => $data['realname'],
                    'phone'     => $data['phone'],
                    'gender'    => $data['gender'],
                    'area'      => $data['region_name'],
                    'addr'      => $data['addr'],
                    'time'      => $data['time'],
                    'time_noon' => $data['time_noon'],
                    'server_id' => $data['server_id'],
                    'userid'    => $order['user']['user_id'],
            );
                    
            //创业者顾客表数据
            $mfData = array(
                    'figure_state'    => '0',
                    'storeid'         => $order['user']['user_id'],
                    'customer_name'   => $data['realname'],
                    'customer_mobile' => $data['phone'],
                    'firsttime'    => gmtime(),
                    'lasttime'     => gmtime(),
                    'gender'       => $data['gender'],
                    'service_mode' => $type,
            );
        
            if($order['measure']['type'] == 6){
                $figure['liangti_id']   = $data['liangti'];
                $figure['liangti_name'] = $data['liangti_name'];
        
                $mfData['liangti_id'] = $data['liangti'];
                $mfData['liangti_name'] = $data['liangti_name'];
                $mfData['id_serve'] = $data['server_id'];
        
            }
            $this->mfData[] = $mfData;
        
            return $figure;
        }else if($type == 5){
            
            $history = $this->mCf->get("figure_sn = '{$data['history_id']}'");
            
            $figure  = array(
                    'goods_sn'    =>$input['goods_sn'],
                    'measure'     => $type,
                    'realname'    => $history['customer_name'],
                    'phone'       => $history['customer_mobile'],
                    'gender'      => $history['gender'],
                    'liangti_id'  => $history['liangti_id'],
                    'liangti_name'  => $history['liangti_name'],
                    'liangti_state' => 1,
                    'history_id'    => $history['figure_sn'],
                    //'area'      => $history['region_name'],
                    //'addr'      => $history['addr'],
                    //'time'      => $history['time'],
                    //'time_noon' => $history['time_noon'],
                    'server_id'    => $history['id_serve'],
                    'userid'       => $order['user']['user_id'],
                    'figure_state' => $history['figure_state'],
                    'modi_time'    => $history['lasttime'],
            );
        
            $jFlag = 0;
            if(isset($this->cloudData)){
                $fData = json_decode($this->cloudData,1);
                if($fData && isset($fData['result']['data'])){
                    $jFlag = 1;
                }
            }
            if($jFlag){
                foreach ($fData['result']['data'] as $key=>$row){
                    if(isset($row['isshow']) && $row['isshow'] == 1){
                        if($key == 'lheight' || $key == 'lweight'){
                            $history[$key] = $history[str_replace('l', '', $key)];
                        }
                        if(intval($history[$key]) <= 0){
                            $this->_error((isset($row['zname']) ? $row['zname'] : '').' - 已有量体数据有误!');
                            return false;
                        }
                    }
                    $figure[$key] = $history[$key];
                }
            }
            return $figure;
        }

    }
    
    /**
     * 响应支付成功 修改订单状态
     * 
     * @author Ruesin
     */
    function respond_notify($order)
    {
        
        $where = " order_id = '{$order['order_id']}' ";
        $data = array();
        
        $res = 1;
        
        $transaction = $this->_mod_order->beginTransaction();
        
        switch ($order['target'])
        {
            case ORDER_ACCEPTED:
                
                $where .= ' AND status=' . ORDER_PENDING;
                 
                if ($order['has_measure'] == '1'){
                    $where .= " AND has_measure = '1' ";
                    $data['status'] = ORDER_WAITFIGURE;
                }else{
                    $mOrderpccron   = &m("orderpccron");
                    $res = $mOrderpccron->_addToList($order['order_id']);
                    if(!$res){
                        for ($tNum = 0;$tNum<5;$tNum++){
                            $res = $mOrderpccron->_addToList($order['order_id']);
                            if($res){
                                $data['status'] = ORDER_ACCEPTED;
                                break;
                            }
                        }
                    }else{
                        $data['status'] = ORDER_ACCEPTED;
                    }
                }
                $data['pay_time']   =   time();
                
            break;
            case ORDER_FINISHED:
                $where .= ' AND status=' . ORDER_SHIPPED;
        	    $data['finished_time'] = time();
            break;
            case ORDER_CANCLED: 
            break;
            default:
                return false;
        }

        if (!$res){
            $this->_mod_order->rollback();
            return false;
        }
        
        $res = $this->_mod_order->edit($where, $data);
        if (!$res){
            $this->_mod_order->rollback();
            return false;
        }
        
        $this->_mod_order->commit($transaction);
        
        return $data['status'];
        return true;
        //return $res;
    }
    
    /**
     * 生成订单号
     *
     * @author Ruesin
     */
    function _gen_order_sn()
    {
        do{
            $timestamp = gmtime();
            $order_sn = date('Ymd', $timestamp) . str_pad(mt_rand(0,9999), 4, '0', STR_PAD_LEFT);
        }while ($this->_mod_order->find("order_sn='{$order_sn}'"));
    
        return $order_sn;
    }
    
    /**
     * 生成订单号
     *
     * @author Ruesin
     */
    function _gen_goods_sn()
    {
        do{
            $goods_sn = date('Ymd') . str_pad(mt_rand(0,9999), 4, '0', STR_PAD_LEFT);
        }while ($this->_mod_ordergoods->find("goods_sn='{$goods_sn}'"));
    
        return $goods_sn;
    }
    /**
     * 生成子订单号  用于 一订单 → 多量体 一量体 → 多商品
     *
     * @author Ruesin
     */
    function _gen_son_sn()
    {
        do{
            $timestamp = gmtime();
            $son_sn = date('Ymd', $timestamp) . str_pad(mt_rand(0,9999), 4, '0', STR_PAD_LEFT);
        }while ($this->_mod_orderfigure->find("son_sn='{$son_sn}'"));
    
        return $son_sn;
    }
    
}


