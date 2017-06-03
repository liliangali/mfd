<?php

/**
 *    支付宝支付方式插件
 *
 *    @author    yhao.bai
 *    @usage    none
 */
class MalipayPayment extends BasePayment
{
    /* 支付宝网关 */
	var $sign_type = "MD5";
	var $req_data;
	var $format;
	var $mysign;
	var $_code = "malipay";
	var $_gateway = 'http://wappaygw.alipay.com/service/rest.htm?';


// 	$this->_config['alipay_account']; //收款账号
// 	$this->_config['alipay_partner']; //收款账号
// 	$this->_config['alipay_key'];
     /*
     *    获取支付表单
     *
     *    @author    yhao.bai
     *    @param     array $order_info  待支付的订单信息，必须包含总费用及唯一外部交易号
     *    @return    array
     */
    function get_payform($order_info, $type ="order")
    {
      
    	// 构造要请求的参数数组，无需改动
    	$subject                    =  "我的订单：{$order_info['order_sn']}"; //支付对象
    	$total                      =  $order_info['final_amount'];  //金额
    	$out_trade_no               =  $this->_get_trade_sn(); //流水号
    	$notify_url                 =  $this->_create_notify_url($order_info['order_sn'],$order_info['extension']); //通知地址
    	$call_back_url              =  $this->_create_return_url($order_info['order_sn'],$order_info['extension']); //返回地址
    	$Service_Create	            = "alipay.wap.trade.create.direct";
    	$Service_authAndExecute		= "alipay.wap.auth.authAndExecute";
    	$format						= "xml";
    	$sec_id						= "MD5";
    	$_input_charset				= "utf-8";
    	$v							= "2.0";

    	$tokenData = array (
    			"req_data" => '<direct_trade_create_req><subject>'.$subject.'</subject><out_trade_no>' . $out_trade_no . '</out_trade_no><total_fee>'.$total.'</total_fee><seller_account_name>'. $this->_config['alipay_account'] . '</seller_account_name><notify_url>' . $notify_url . '</notify_url><out_user></out_user><merchant_url></merchant_url><call_back_url>' . $call_back_url . '</call_back_url></direct_trade_create_req>',
    			"service" => $Service_Create,
    			"sec_id" => $sec_id,
    			"partner" => $this->_config['alipay_partner'],
    			"req_id" => date ( "Ymdhms" ),
    			"format" => $format,
    			"v" => $v,
    			'_input_charset' => 'utf-8',
    	);

//     	$this->log_result(json_encode($tokenData));

    	$token = $this->alipay_wap_trade_create_direct ( $tokenData, $this->_config['alipay_key'], $sec_id );

    	// 构造要请求的参数数组，无需改动
    	$postData = array (
    			"req_data" => "<auth_and_execute_req><request_token>" . $token . "</request_token></auth_and_execute_req>",
    			"service" => $Service_authAndExecute,
    			"sec_id" => $sec_id,
    			"partner" => $this->_config['alipay_partner'],
    			"call_back_url" => $call_back_url,
    			"format" => $format,
    			"v" => $v,
    			'_input_charset' => 'utf-8',
    	);
    	
    	// 调用alipay_Wap_Auth_AuthAndExecute接口方法，并重定向页面
    	$this->alipay_Wap_Auth_AuthAndExecute ( $postData, $this->_config['alipay_key'] );

    }

    /**
     * 调用alipay_Wap_Auth_AuthAndExecute接口
     */
    function alipay_Wap_Auth_AuthAndExecute($parameter, $key) {
    	$this->parameter = $this->para_filter ( $parameter );
    	$sort_array = $this->arg_sort ( $this->parameter );
    	$this->sign_type = $this->parameter ['sec_id'];
    	$this->_key = $key;
    	$this->mysign = $this->build_mysign ( $sort_array, $this->_key, $this->sign_type );
    	$RedirectUrl = $this->_gateway . $this->create_linkstring ( $this->parameter ) . '&sign=' . urlencode ( $this->mysign );

    	// 跳转至该地址
    	Header ( "Location: $RedirectUrl" );
    }

    /**
     * 创建alipay.wap.trade.create.direct接口
     */
    function alipay_wap_trade_create_direct($parameter, $key, $sign_type) {
    	$this->_key = $key; // MD5校验码
    	$this->sign_type = $sign_type; // 签名类型，此处为MD5
    	$this->parameter = $this->para_filter ( $parameter ); // 除去数组中的空值和签名参数
    	$this->req_data = $parameter ['req_data'];
    	$this->format = $this->parameter ['format']; // 编码格式，此处为utf-8
    	$sort_array = $this->arg_sort ( $this->parameter ); // 得到从字母a到z排序后的签名参数数组
    	$this->mysign = $this->build_mysign ( $sort_array, $this->_key, $this->sign_type ); // 生成签名
    	$this->req_data = $this->create_linkstring ( $this->parameter ) . '&sign=' . urlencode ( $this->mysign ); // 配置post请求数据，注意sign签名需要urlencode

    	// Post提交请求
    	$result = $this->post ( $this->_gateway );

    	// 调用GetToken方法，并返回token
    	return $this->getToken ( $result );
    }

    /**
     * 返回token参数
     * 参数 result 需要先urldecode
     */
    function getToken($result) {
    	$result = urldecode ( $result ); // URL转码
    	$Arr = explode ( '&', $result ); // 根据 & 符号拆分

    	$temp = array (); // 临时存放拆分的数组
    	$myArray = array (); // 待签名的数组
    	// 循环构造key、value数组
    	for($i = 0; $i < count ( $Arr ); $i ++) {
    		$temp = explode ( '=', $Arr [$i], 2 );
    		$myArray [$temp [0]] = $temp [1];
    	}

    	$sign = $myArray ['sign']; // 支付宝返回签名
    	$myArray = $this->para_filter ( $myArray ); // 拆分完毕后的数组

    	$sort_array = $this->arg_sort ( $myArray ); // 排序数组
    	$this->mysign = $this->build_mysign ( $sort_array, $this->_key, $this->sign_type ); // 构造本地参数签名，用于对比支付宝请求的签名

    	if ($this->mysign == $sign) 		// 判断签名是否正确
    	{
    		return $this->getDataForXML ( $myArray ['res_data'], '/direct_trade_create_res/request_token' ); // 返回token
    	} else {
    		echo ('签名不正确'); // 当判断出签名不正确，请不要验签通过
    		return '签名不正确';
    	}
    }

    /**
     * 通过节点路径返回字符串的某个节点值
     * $res_data——XML 格式字符串
     * 返回节点参数
     */
    function getDataForXML($res_data,$node)
    {
    	$xml = simplexml_load_string($res_data);
    	$result = $xml->xpath($node);

    	while(list( , $node) = each($result))
    	{
    		return $node;
    	}
    }

    /**
     * PHP Crul库 模拟Post提交至支付宝网关
     * 返回 $data
     */
    function post($gateway_url) {
    	$ch = curl_init ();
    	curl_setopt ( $ch, CURLOPT_URL, $gateway_url ); // 配置网关地址
    	curl_setopt ( $ch, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    	curl_setopt ( $ch, CURLOPT_POST, 1 ); // 设置post提交
    	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $this->req_data ); // post传输数据
    	$data = curl_exec ( $ch );
    	curl_close ( $ch );
    	return $data;
    }
    /**生成签名结果
     * $array要签名的数组
    * return 签名结果字符串
    */
    function build_mysign($sort_array,$key,$sign_type = "MD5") {
    	$prestr = $this->create_linkstring($sort_array);     	//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
    	$prestr = $prestr.$key;							//把拼接后的字符串再与安全校验码直接连接起来
    	$mysgin = $this->sign($prestr,$sign_type);			    //把最终的字符串签名，获得签名结果
    	return $mysgin;
    }

    /********************************************************************************/

    /**把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * $array 需要拼接的数组
    * return 拼接完成以后的字符串
    */
    function create_linkstring($array) {
    	$arg  = "";
    	while (list ($key, $val) = each ($array)) {
    		$arg.=$key."=".$val."&";
    	}
    	$arg = substr($arg,0,count($arg)-2);		     //去掉最后一个&字符
    	return $arg;
    }


    /**签名字符串
     * $prestr 需要签名的字符串
    * $sign_type 签名类型，也就是sec_id
    * return 签名结果
    */
    function sign($prestr,$sign_type) {
    	$sign='';
    	if($sign_type == 'MD5') {
    		$sign = md5($prestr);
    	}elseif($sign_type =='DSA') {
    		//DSA 签名方法待后续开发
    		die("DSA 签名方法待后续开发，请先使用MD5签名方式");
    	}else {
    		die("支付宝暂不支持".$sign_type."类型的签名方式");
    	}
    	return $sign;
    }

    /********************************************************************************/

    /**除去数组中的空值和签名参数
     * $parameter 签名参数组
    * return 去掉空值与签名参数后的新签名参数组
    */
    function para_filter($parameter) {
    	$para = array();
    	while (list ($key, $val) = each ($parameter)) {
    		if($key == "sign" || $key == "sign_type" || $val == "")continue;
    		else	$para[$key] = $parameter[$key];
    	}
    	return $para;
    }

    /********************************************************************************/

    /**对数组排序
     * $array 排序前的数组
    * return 排序后的数组
    */
    function arg_sort($array) {
    	ksort($array);
    	reset($array);
    	return $array;
    }

    /**
     *    返回通知结果
     *
     *    @author    yhao.bai
     *    @param     array $order_info
     *    @return    array
     */
    function verify_notify($order_info)
    {
        /* 初始化所需数据 */
        $notify =   $this->_get_notify();
        
        $model_paybill =& m("paymentbills");
        
        $mOd = &m('order');
        
        if(empty($notify)){
        	$this->_error('sign_inconsistent');
        	return false;
        }

        $get          = $this->para_filter($notify);	    //对所有GET反馈回来的数据去空
        $sort_get     = $this->arg_sort($get);		    //对所有GET反馈回来的数据排序
        $this->mysign = $this->build_mysign($sort_get, $this->_config['alipay_key'], $this->sign_type);    //生成签名结果
        if ($this->mysign == $notify["sign"]) {
        	$order_status = ORDER_PENDING;

        	if($notify ['result'] == 'success'){
        		$order_status = STORE_ACCEPTED;
        	}
        	$paymentsn = $model_paybill->get("payment_sn='{$notify['out_trade_no']}'");
        
        	$info = $order = $mOd->get("order_sn = '{$paymentsn['order_sn']}' ");
        	
        	if ($info['out_trade_sn'] != $notify['out_trade_no'])
        	{
        		/* 通知中的订单与欲改变的订单不一致 */
        		$this->_error('order_inconsistent');

        		return false;
        	}

        	//至此，说明通知是可信的，订单也是对应的，可信的

        	$billdata = array(
        			'status'    => $notify['result'],
        			'end_time'	=> time(),
        			'buyer_name' => $notify['buyer_email'],
        	);

        	$this->updateBill($notify['out_trade_no'], $billdata);


        	return array(
        		'target'    =>  $order_status,
        	);

        }else {
        	$this->_error('sign_inconsistent');
        	return false;
        }
    }


    function veryfy_malipay($order_info){
        
    	/* 初始化所需数据 */
    	$notify =   $this->_get_notify();
    	
     // $notify = json_decode('{"service":"alipay.wap.trade.create.direct","sign":"2522c1a361bd85929d1b415f72120eb0","sec_id":"MD5","v":"1.0","notify_data":"&lt;notify&gt;&lt;payment_type&gt;1&lt;\/payment_type&gt;&lt;subject&gt;\u6211\u7684\u8ba2\u5355\uff1a20001518180362&lt;\/subject&gt;&lt;trade_no&gt;2015070100001000490060482478&lt;\/trade_no&gt;&lt;buyer_email&gt;byhere@126.com&lt;\/buyer_email&gt;&lt;gmt_create&gt;2015-07-01 15:59:12&lt;\/gmt_create&gt;&lt;notify_type&gt;trade_status_sync&lt;\/notify_type&gt;&lt;quantity&gt;1&lt;\/quantity&gt;&lt;out_trade_no&gt;14357375386247&lt;\/out_trade_no&gt;&lt;notify_time&gt;2015-07-01 16:03:40&lt;\/notify_time&gt;&lt;seller_id&gt;2088611206542452&lt;\/seller_id&gt;&lt;trade_status&gt;TRADE_SUCCESS&lt;\/trade_status&gt;&lt;is_total_fee_adjust&gt;N&lt;\/is_total_fee_adjust&gt;&lt;total_fee&gt;0.01&lt;\/total_fee&gt;&lt;gmt_payment&gt;2015-07-01 15:59:13&lt;\/gmt_payment&gt;&lt;seller_email&gt;m18806390183@163.com&lt;\/seller_email&gt;&lt;price&gt;0.01&lt;\/price&gt;&lt;buyer_id&gt;2088202412480494&lt;\/buyer_id&gt;&lt;notify_id&gt;5d3904fd7aae387e2713355e9fb2dbdb4q&lt;\/notify_id&gt;&lt;use_coupon&gt;N&lt;\/use_coupon&gt;&lt;\/notify&gt;"}',1);
    	if(empty($notify)){
    		return false;
    	}


    	//$this->log_result(json_encode($notify));
    	$notify['notify_data'] = htmlspecialchars_decode($notify['notify_data']);

    	//此处为固定顺序，支付宝Notify返回消息通知比较特殊，这里不需要升序排列
    	$notifyarray = array(
    			"service"		=> $notify['service'],
    			"v"				=> $notify['v'],
    			"sec_id"		=> $notify['sec_id'],
    			"notify_data"	=> $notify['notify_data']
    	);


    	$this->mysign = $this->build_mysign($notifyarray,$this->_config['alipay_key'],$this->sign_type);

    	if ($this->mysign == $notify["sign"]) {
    		$order_status = STORE_ACCEPTED;

    		$status = (string)$this->getDataForXML ( $notify['notify_data'], '/notify/trade_status' ); // 返回token
    		/*
    		if ($status == 'TRADE_FINISHED') { // 交易成功结束
    			$order_status = ORDER_ACCEPTED;
    		}*/

			/* 按通知结果返回相应的结果 */
			switch ($status)
			{
				case 'WAIT_SELLER_SEND_GOODS':      //买家已付款，等待卖家发货
				    if($order_info['has_measure'] == '1'){
				        $order_status = ORDER_WAITFIGURE;   //待量体
				    }else{
				        $order_status = ORDER_ACCEPTED;   //已付款
				    }
					
				break;

				case 'TRADE_SUCCESS':  //交易成功  支付成功
			         
				    if($order_info['has_measure'] == '1'){
				        $order_status = ORDER_WAITFIGURE;   //待量体
				    }else{
				        $order_status = ORDER_ACCEPTED;   //已付款
				    }

					break;

				case 'WAIT_BUYER_CONFIRM_GOODS':    //卖家已发货，等待买家确认

					$order_status = ORDER_SHIPPED;
				break;

				case 'TRADE_FINISHED':              //交易结束 完成
					if ($order_info['status'] == ORDER_PENDING)
					{
						/* 如果是等待付款中，则说明是即时到账交易，这时将状态改为已付款 */
    					if($order_info['has_measure'] == '1'){
    				        $order_status = ORDER_WAITFIGURE;   //待量体
    				    }else{
    				        $order_status = ORDER_ACCEPTED;   //已付款
    				    }
					}
					else
					{
						/* 说明是第三方担保交易，交易结束 */
						$order_status = ORDER_FINISHED;
					}
				break;
				case 'TRADE_CLOSED':                //交易关闭
					$order_status = ORDER_CANCLED;
				break;

				default:
					$this->_error('undefined_status');
					return false;
				break;
			}

    		$out_trade_no = (string)$this->getDataForXML ( $notify['notify_data'], '/notify/out_trade_no' );


    		$total_fee = (string)$this->getDataForXML ( $notify['notify_data'], '/notify/total_fee');


    		$buyer_email = (string)$this->getDataForXML ( $notify['notify_data'], '/notify/buyer_email');
    		
    		//$this->log_result(json_encode($order_info));
    		if ($order_info['out_trade_sn'] != $out_trade_no && !$order_info['out_trade_sns'][$out_trade_no])
    		{
    			return false;
    		}
            
    		if ($order_info['final_amount'] != $total_fee)
    		{
    			return false;
    		}

    		$billdata = array(
    				'status'    => $status,
    				'end_time'	=> time(),
    				'buyer_name' => $buyer_email,
    		);

    		$this->updateBill($out_trade_no, $billdata);

    		return array(
				'target'    =>  $order_status,
    		);

    	}else {
    		$this->_error('sign_inconsistent');
    		return false;
    	}
    }

    /**日志消息,把支付宝返回的参数记录下来
     * 请注意服务器是否开通fopen配置
    */
    function log_result($word) {
    	$fp = fopen("log.php","a");
    	flock($fp, LOCK_EX) ;
    	fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
    	flock($fp, LOCK_UN);
    	fclose($fp);
    }
}

?>