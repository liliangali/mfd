<?php
/**
 * 订单操作类
 * 
 * @date 2015-8-6 上午10:08:44
 * @author Ruesin
 */
class Orders extends Object{
	
	function __construct(){
		
	}
	
	//获取快递信息
	function getShipInit($order_info,$def='')
	{
	    $return = ['shippings'=>'','defship'=>''];

	    //物流信息
	    $shipping_mod = m('shipping');
	    $shippingarea_mod = m('shippingarea');
	    $shippings = $shipping_mod->find(array(
	        'conditions'    => " 1 = 1 AND enabled = 1 ",
	        'order'         => "sort_order desc",
	        'index_key'  => "shipping_id",
	    ));
	    // 根据订单 选择物流公司  显示默认
	    if ($order_info['shipping_id'] && isset($shippings[$order_info['shipping_id']]))
	    {
	        //默认物流
	        $defship = $shippings[$order_info['shipping_id']];
	    }else{
	        foreach ((array)$shippings as $row)
	        {
	            if ($row['is_default'] && $row['is_default'])
	            {
	                $defship = $shippings[$row['shipping_id']];
	                break;
	            }
	        }
	        if (!$defship)
	        {
	            $defship = current($shippings);
	        }
	    }
	    
	    //指定物流公司
	    if ($def && isset($shippings[$def]))
	    {
	        $defship = $shippings[$def];
	    }
	    
	    $regionData = explode(',', $order_info['ship_area_id']);
	    
	    //根据默认 物流公司 + 收货地址 城市 去取 有没有指定区域价格
	    if (isset($regionData[2]) && $defship['shipping_id'])
	    {
	        //省市区第三级（一级是国家）
	        $shipping_area = $shippingarea_mod->get(array(
	            'conditions'    => " 1 = 1 AND region_id = '{$regionData[2]}' and  shipping_id = '{$defship['shipping_id']}'",
	            'order'         => "area_id desc",
	            'index_key'  => "area_id",
	        ));
	        if ($shipping_area)
	        {
	            $defship['first_money'] = $shipping_area['first_price'];        //首重价格
	            $defship['step_money'] = $shipping_area['step_price'];          //续重价格
	            $defship['first_weight'] = $shipping_area['first_weight'];      //首重 重量克
	            $defship['step_weight'] = $shipping_area['step_weight'];        //续重 重量克
	            $defship['area_id'] = $shipping_area['area_id'];                //指定地区 特殊处理.
	        }
	    }
	    
	    //根据订单 重量 计算  首重价格 + （重量-首重）*续重价格 ?????  
	    if (!$defship['is_fress']) //收费
	    {


	        if ($defship && $order_info['weight'] > 0 )
	        {
	            $defship['post_fee'] = $this->freightCnt($order_info['weight'],$defship);
	        }
	    }
	    else //免费
	    {
	        $defship['post_fee'] = 0;
	    }

	    $return['shippings'] = $shippings;
	    $return['defship'] = $defship;
	    return $return;
	}
	//根据物流配置、重量计算运费价格
    function freightCnt($weight,$ship,$is_try=0)
    {
        $money = 9.9;
        if (!is_array($ship)) return 999999.99;
        if($is_try)
        {
            if ($ship['is_fress'] == 1)//免费
            {
                return $money;
            }
            if (($ship['first_weight']) >= ($weight))
            {
                return round($ship['first_money'] + $money,2);
            }
            return @round((($weight)-$ship['first_weight'])/$ship['step_weight']*$ship['step_money'] + $ship['first_money'] + $money,2);
        }
        else
        {
            if ($ship['is_fress'] == 1)//免费
            {
                return 0.00;
            }
            if (($ship['first_weight']) >= ($weight)) return round($ship['first_money'],2);
            return @round((($weight)-$ship['first_weight'])/$ship['step_weight']*$ship['step_money'] + $ship['first_money'],2);
        }
    }
	/**
	 * 订单取消的各操作
	 * 
	 * @date 2015-8-6 上午11:17:58
	 * @author Ruesin
	 */
	function cancelById($order_id = 0,$order = array()){ 
	    	    
	    if(!$order_id) return false;
	    
	    if(!$order){
	        $mOrder = &mr("order");
	        $order = $mOrder->get($order_id);
	    }
	    
	    if(!$order) return false;
	    
	    imports("diys.lib");
	    $diys = new Diys();
	    $customs = $diys->_customs();
	    
	    
	    $mOgoods = &mr('ordergoods');
	    $goods = $mOgoods->find("order_id = '{$order_id}'");
	    
	    //==== 库存返还
	    foreach ($goods as $row){
	        $stc[$row['fabric']] += $row['quantity']*($customs[$row['cloth']]['fabric_m']);
	    }
	    $mFabirc = &mr('fabric');
	    foreach ($stc as $key=>$val){
	        $mFabirc->setInc(" CODE = '{$key}' AND is_sale = '1'",'STOCK',$val);
	    }
	    
	    $mMem = &m('member');
	    $member = $mMem->get($order['user_id']);
	    
	    //==== 余额返还
	    if($order['money_amount'] && intval($order['money_amount']) > 0){
	        
	        $money = $member['money'] + $order['money_amount'];
	        $frozen = $member['frozen'] - $order['money_amount'];
	        if($frozen >= 0)
	        {
	            $mMem->edit(" user_id = '{$order['user_id']}' ", array('money'=>$money,'frozen'=>$frozen),0);
	            $cashLog[] = array(
	                'name'     => "订单取消返还余额(订单号：{$order['order_sn']})",
	                'order_id' => $order['order_id'],
	                'order_sn' => $order['order_sn'],
	                'user_id'  => $order['user_id'],
	                'minus'    => 3,
	                'cash_money' => $order['money_amount'],
	                'add_time'   => gmtime(),
	                'order_money' => $order['order_amount'],
	                'type'  => 4,
	            );
	        }
	        
	        
	    }
	    
	    //==== 现金支付返还  add by 小五 
	    /* 代码注释 @荣平和运维商榷 1、线下财务处理，2、支付宝里的退款功能  http://pms.mfd.cc/www/index.php?m=task&f=view&id=334 */
/* 	    if($order['out_trade_sn']){
	    	$bill_mod = m('paymentbills');
	    	$real = $bill_mod->get(" `end_time` > 0 and order_sn = '{$order['order_sn']}'");
	    	if (isset($real['payment_sn'])){
	    		if($order['final_amount'] > 0){
	    			$mMem->setInc(" user_id = '{$order['user_id']}'",'money',$order['final_amount']);
	    			$cashLog[] = array(
	    					'name'     => "订单取消返还支付金额(订单号：{$order['order_sn']})",
	    					'order_id' => $order['order_id'],
	    					'order_sn' => $order['order_sn'],
	    					'user_id'  => $order['user_id'],
	    					'minus'    => 9,
	    					'cash_money' => $order['final_amount'],
	    					'add_time'   => gmtime(),
	    					'order_money' => $order['order_amount'],
	    					'type'  => 4,
	    			);
	    		}
	    	}
	    } */
	    
	    //处理麦富迪币
	    if($order['coin'] > 0){
	    
	        $coin  = $member['coin'] + $order['coin'];
	        $freezes_coin = $member['freezes_coin'] - $order['coin'];
	        if($freezes_coin >= 0)
	        {
	            $mMem->edit(" user_id = '{$order['user_id']}' ", array('coin'=>$coin,'freezes_coin'=>$freezes_coin),0);
	            $cashLog[] = array(
	                'name'     => "取消订单返麦富迪币(订单号：{$order['order_sn']})",
	                'order_id' => $order['order_id'],
	                'order_sn' => $order['order_sn'],
	                'user_id'  => $order['user_id'],
	                'minus'    => 3,
	                'cash_money' => $order['coin'],
	                'add_time'   => gmtime(),
	                'order_money' => $order['order_amount'],
	                'type'  => 2,
	            );
	        }
	    
	    }
	    
	    if($cashLog){
	        $mCashLog = &mr('ordercashlog');
	        $mCashLog->add(addslashes_deep($cashLog),false,0);
	    }
	    
	    //==== 酷卡
	     
	    $mOrderKuka = &m('orderkuka');
	    $kukas  = $mOrderKuka->find("order_id = '{$order_id}'");
	    if($kukas){
	        foreach ($kukas as $row){
	            $kukaIds[$row['k_id']] = $row['k_id'];
	        }
	        
	        $mOrderKuka->edit("order_id = '{$order_id}'",array('is_active'=>0));
	         
	        $mKuka = &m('special_code');
	        $mKuka->edit(db_create_in($kukaIds,'id'),array('is_order' => 0));
	    }

	    
	    
	    //==== 抵扣券
 	    $mOd = &mr('orderdebit');
 	    $dbs = $mOd->find("order_id = '{$order_id}'");
 	    if ($dbs){
 	        foreach ($dbs as $row){
 	            $dIds[$row['d_id']] = $row['d_id'];
 	        }
 	        $mDebit = &mr('debit');
 	        $mDebit->edit(db_create_in($dIds,'id'), array('is_used'=>'0'),0);
 	    }
	    
	    //== 首单名额
	    $mOf = &mr('orderfirstlog');
	    $mOf->edit("order_id = '{$order_id}'", array('is_active'=>'0'),0);
	    
	    //======== 买一赠一名额
	    if($order['one_num']){
	        $order_mod = &m('order');
	        $order_mod->edit("order_id = '{$order_id}' " , array('one_status'=>'0'),0);
	    }
	    
	    //=====  liang.li  流推送信息表状态改为3  =====
	    $order_mtm_logs_mod = m('ordermtmlogs');
	    $order_mtm_logs_mod->edit("order_id = '{$order_id}' " ,array('logistics'=>3),0);

		/*add by 小五 订单取消发送短信*/
		$phone = $smsStr =  '';
		$smsStr = '您的订单'.$order['order_sn'].'已取消，用于结算的';
		$phone = empty($order['ship_mobile']) ? $order['user_name'] : $order['ship_mobile'];
		if($kukas){
			$kuka = '';
			foreach($kukas as $v){
				$kuka +=$v['k_money'];
			}
			$smsStr .= intval($kuka).'元酷卡、';
		}
		if($order['money_amount']){
			$smsStr .= intval($order['money_amount']).'元余额、';
		}
		if($order['coin']){
			$smsStr .= intval($order['coin']).'麦富迪币、';
		}
		$smsStr .= '已退回相关账户，请注意查收【麦富迪定制】';
		/*不记录返回值*/
		SendSms($phone,$smsStr,'cancel');
        
	    return true;
	}
    	
    /**
     * 获取所有品类的刺绣信息
     * 
     * @author Ruesin
     */
    function _format_embs($type = 'code'){
        $mEp  = &m('dict_embs_parent');
        $p = $mEp->find(array(
        	   'conditions' => " is_show = '1' ",
               'order'      => ' rank ASC',
        ));
        
        foreach ((array)$p as $row){
            $pIds[$row['id']] = $row['id'];
            $res[$row['cCode']][$row['id']] = $row;
            $resId[$row['cId']][$row['id']] = $row;
        }
        if($pIds){
            $mDict = &m('dict');
            $e = $mDict->find(array(
                    'conditions' => " is_display = '1' AND ".db_create_in($pIds,'parentid'),
            ));
        }
        
        foreach ((array)$e as $row){

            if($p[$row['parentid']]){
                $row['image'] = "http://img.diy.mfd.cn/process/{$row['parentid']}/{$row['id']}_S.png";
                //$row['parentid'];
                $res[$p[$row['parentid']]['cCode']][$row['parentid']]['list'][$row['id']] = $row;
                $resId[$p[$row['parentid']]['cId']][$row['parentid']]['list'][$row['id']] = $row;
            }
        }
        
        if($type == 'id'){
            return $resId;
        }
        return $res;
    }
    
	
}