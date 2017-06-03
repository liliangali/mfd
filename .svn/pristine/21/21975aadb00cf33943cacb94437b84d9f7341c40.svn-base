<?php
use Cyteam\Shop\Type;
use Cyteam\Shop\Type\Types;
/**
 * 支付响应 新版
 *
 * @author Ruesin
 */
class PaynotifysApp extends PaycenterbaseApp
{
    /**
     * 支付完成后返回的URL
     * 
     * @author Ruesin
     */
    function index(){

        //$this->_user_id = $_SESSION['user_info']['user_id'];
    	$args = $this->get_params();
        $sn   = isset($args[0]) ? trim($args[0]) : '';
        if (!$sn){
			$this->assign('error_msg', '无效的通知请求');
			$this->display('orders/error.html');
			return false;
        }
        $mOd = &m('order');
        $order = $order = $mOd->get("order_sn = '{$sn}' "); //AND user_id = '{$this->_user_id}'  //坑爹的回跳没session了，也懒得在url里加参数了，直接调取吧，反正也没有什么操作。
        if (empty($order)){
			$this->assign('error_msg', '没有该订单');
			$this->display('orders/error.html');
			return false;
        }
        
        $model_payment =& m('payment');
        
        $payment_info  = $model_payment->get("payment_code='{$order['payment_code']}'");
        if (empty($payment_info)){
			$this->assign('error_msg', '没有指定的支付方式');
			$this->display('orders/error.html');
			return false;
        }

        /* 调用相应的支付方式 */
        $payment = $this->_get_paymentm($order['payment_code'], $payment_info);
     
        /* 获取验证结果 */
        //$notify_result = $payment->verify_notify($order_info);
        //手机支付宝主动请求和被动请求的参数不一样，验证方法也不一样，但是这个页面就是提示支付成功的，不对订单做任何操作，所以可以屏蔽掉
// 		$notify_result = $order['payment_code'] == 'malipay' ? $payment->veryfy_malipay($order) : $payment->verify_notify($order, true);
		
//         if ($notify_result === false){
// 			$this->assign('error_msg', '支付失败');
// 			$this->display('orders/error.html');
// 			return false;
//         }
        //$notify_result['user_name'] = $order['user_name'];
        #TODO 临时在此也改变订单状态为方便调试，实际发布时应把此段去掉，订单状态的改变以notify为准
        //$this->_change_order_status($order['order_id'], $order['extension'], $notify_result);
		
        
		//计算订单预计时间
        $del_data = strtotime('+10 day');
		if($order['has_measure']){
		    $mFg = &m('orderfigure');
		    $fData = $mFg->get("order_id = '{$order['order_id']}'");
		    if($fData['time']){
		        $del_data = strtotime($fData['time'])+864000;
		    }
		}
		
		
        /* 只有支付时会使用到return_url，所以这里显示的信息是支付成功的提示信息 */
        /*  支付完成之后 判断 有无实名认证 过*/
        $auth_mod = &m('auth');
        $authinfo = $auth_mod->get("user_id ='{$order['user_id']}'");
        $this->_curlocal(LANG::get('pay_successed'));
//        $this->assign('notify',$notify_result);
        $this->assign('order', $order);
        $this->assign('payment', $payment_info);
		$this->assign('del_data', $del_data);
        $this->assign('auth_id', $authinfo['id']);
        $this->display('orders/payment/success.html');
    }
    
    /**
     * 支付成功响应地址
     * 
     * @author Ruesin
     */
    function notify(){
		$args = $this->get_params();
        $order_sn   = isset($args[0]) ? trim($args[0]) : ''; 
        
        if (!$order_sn){
            //echo 'success';
            echo 'fail';
            return;
        }
        
        $mOd = &m('order');
        $order_info = $order = $mOd->get("order_sn = '{$order_sn}' ");
        
        if (empty($order_info)){
            echo 'fail';
            return;
        }
        
        if ($order_info['status'] != ORDER_PENDING){
            echo 'fail';
            return;
        }
        
        $mPayBill = &m('paymentbills');
        $order_info['out_trade_sns'] = $mPayBill->find("order_sn = '{$order_sn}'");  //member_id pay_id
        
        $model_payment =& m('payment');
        
        $payment_info  = $model_payment->get("payment_code='{$order_info['payment_code']}'");
        if (empty($payment_info)){
            echo 'fail';
            return;
        }

        $payment = $this->_get_paymentm($order_info['payment_code'], $payment_info);

        $notify_result = $order_info['payment_code'] == 'malipay' ? $payment->veryfy_malipay($order_info) : $payment->verify_notify($order_info, true);

        if ($notify_result === false){
            $payment->verify_result(false);
            return;
        }

        $notify_result['user_name'] = $order_info['user_name'];
        $notify_result['order_sn'] = $order_info['order_sn'];
        //改变订单状态
        $changeRes = $this->_change_order_status($order_info['order_id'], $order_info['extension'], $notify_result);
        if ($changeRes)
        {
            //支付成功短信通知
            fukuan($order_info['order_id']);
            
            $payment->verify_result(true);
            $this->lastSomeThing($order_info);
        }
    }
	
	
    /**
     *    改变订单状态
     *
     *    @author    yhao.bai
     *    @param     int $order_id
     *    @param     string $order_type
     *    @param     array  $notify_result
     *    @return    void
     */
    function _change_order_status($order_id, $order_type = "normal", $notify_result)
    {
        
        /* 将验证结果传递给订单类型处理 */
        $order_type  =& ot($order_type);
        return $order_type->respond_notify($order_id, $notify_result);    //响应通知
        
    }
    
    public function lastSomeThing($order_info)
    {
        
        
        $sn = $order_info['out_trade_sn'];
        $_mod_bills = &m("paymentbills");
        if ($sn)
        {
            $data['status'] = "TRADE_SUCCESS";
            $data['end_time'] = time();
            $_mod_bills->edit("payment_sn = $sn",$data);
        }
        $orderLogs[] = [
            'order_id' => $order_info['order_id'],
            'op_id'    => $order_info['user_id'],
            'op_name'  => $order_info['user_name'],
            'from'     => $order_info['status'],
            'to'       => ORDER_ACCEPTED,
            'behavior' => 'payment',
            'remark'   => '在线支付成功，支付单号为:'.$order_info['out_trade_sn'],
        ];

        import('shopCommon');
        ShopCommon::recordLogs($orderLogs);
        ShopCommon::recordFinance($order_info);
        ShopCommon::give_debit($order_info['final_amount'],$order_info['user_id']);
        ShopCommon::f_goods($order_info);
        //===== 执行成功 推送mes       =====
        $goods = Types::createObj("fdiy");
        $res = $goods->mesf($order_info['order_id']);
		//订单写入数据库
        $add_in=$this->add_order_in($order_info);
        
    }
    /*
     * 订单写入电商仓库
     * */
    function add_order_in($order_info){
        $region_mod=m('region');
		$goods_mod=m('goods');
		 $orders_mod=m('order');
        $regions=$region_mod->find('1=1');
        $region='';
        if($regions){
            foreach($regions as $k1=>$v1){
                $region[$v1['region_id']]=$v1['region_name'];
                 
            }
        }
        $ordergoods_mod=m('ordergoods');
        $order_goods=$ordergoods_mod->find("order_id='{$order_info['order_id']}'");
      
        if($order_goods){
            $ins='0';
        
            foreach($order_goods as $key=>$val){
                if($val['type']=='fdiy'){
                    $ins +=1;
                }	
	   $params=json_decode($val['params'],true); 
	    $bns=$params['oProducts']['goods_id'];
		$goodsn=$goods_mod->get("goods_id='{$bns}'");
		
		if($goodsn['bn']){
			$order_goods[$key]['goodsn']=$goodsn['bn'];	  
	
		}else{
			$order_goods[$key]['goodsn']='';
	
		}				
		
            }
        }
        if($ins==0){
            require './../cron/edbdemo1.php';
            $cl=new EdbDemo();
            if($order_info['ship_area_id']){
                $order_region=explode(',' , $order_info['ship_area_id']);
            }
            if($order_region){
                $region1=$region[$order_region[0]];
                $region2=$region[$order_region[1]];
                $region3=$region[$order_region[2]];
            }
           
            $re=$cl->edbTradeAdd($order_info,$num,$order_goods,$region1,$region2,$region3); //添加订单
			 if($re){
                $res=json_decode($re, true);	
				if($res){
					 foreach($res as $key=>$row){
					if($row['items']['item'][0]['is_success']=='True'){
						
						  $edData = array(
							'mes_status'   =>1,
							);
							
						  $orders_mod->edit("order_sn = '{$order_info['order_sn']}'" ,$edData);
					  
						
					}else{
							$edData = array(
							'mes_status'   =>2,
							);
					
					     $orders_mod->edit("order_sn = '{$order_info['order_sn']}'" ,$edData);
					
					}

				   }
				}
					
				  
			   }
			
        }
      
    } 
    
}



