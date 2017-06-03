<?php
/**
 * 新版普通订单
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: news.otype.php 12195 2015-11-28 07:49:23Z liugx $
 * @copyright Copyright 2015 redcollar
 */
class NewsOrder extends BaseOrder
{
    var $_name = 'normal';
 
    function __construct(){
		parent::__construct();
    }
    
    /**
     *    查看订单
     *
     *    @author Ruesin
     */
    function get_order_detail($order_id, $order_info)
    {
    	if (!$order_id)
    	{
    		return array();
    	}
    
    	/* 获取商品列表 */
    	$data['goods_list'] =   $this->_get_goods_list($order_id);
    
    	/* 配送信息 */
    	$data['order_extm'] =   $this->_get_order_extm($order_id);
    
    	/* 支付方式信息 */
    	if ($order_info['payment_id'])
    	{
    		$payment_model      =& m('payment');
    		$payment_info       =  $payment_model->get("payment_id={$order_info['payment_id']}");
    		$data['payment_info']   =   $payment_info;
    	}
    
    	/* 订单操作日志 */
    	$data['order_logs'] =   $this->_get_order_logs($order_id);
    
    	return array('data' => $data);
    }
    
    
    function submit($data){
    	
    	extract($data);
    	
    	/* 订单基本信息 */
    	$baseinfo = $this->_handle_order_info($_order, $_cart);
		if(!$baseinfo) return false;

		/* 保存订单信息 */
    	$res = $order_id = $this->_mod_order->add($baseinfo);
    
    	if (!$order_id){
    		$this->_error('create_order_failed');
    		return false;
    	}
    	

    	$goods = $this->_format_order_goods($_cart['object']);
    	
    	
    	$mtmN = $hasPrice = 0;
    	$countGoods = count($goods);
    	//商品数据  包括工艺 刺绣等
    	foreach ((array)$goods as $key => $value)
    	{
    	    @$price = $baseinfo['final_amount']*($value['subtotal']/$this->goodsAmount);
    	    
    		$goods_items[] = array(
    				'order_id'      => $order_id,
    		        'son_sn'        => isset($figure['son_sn']) ? $figure['son_sn'] : '',
    		        'goods_id'      => $value['params']['oProducts']['product_id'], //货品id
    				'goods_name'    => $value['goods']['name'],
    		        
    		        //为了价格统一  使用比率计算出来的价格
    				'price'         => $value['newPrice']['price'],  //订单显示价格   为各种折扣之后的最终价格
    		        'markprice'     => isset($value['newPrice']['markprice']) ? $value['newPrice']['markprice'] : $value['newPrice']['price'],  //吊牌价 为不打折之前的市场价
    		        //'mtm_price'     => (++$mtmN == $countGoods) ? ceil(intval($price) + $ys) : intval($price),
    		        'mtm_price'     => (++$mtmN == $countGoods) ? $baseinfo['final_amount']-$hasPrice : intval($price),
    				'quantity'      => $value['quantity'],
    		        'subtotal'      => $value['newPrice']['subtotal'],
    		        
    		        //初始价格 不管是否为活动
    		        'nprice'         => $value['goods']['nPrice'],
    		        'nmarkprice'     => isset($value['goods']['nMarkprice']) ? $value['goods']['nMarkprice'] : $value['goods']['nPrice'],
    		        
    		        //订单流程中的价格 有可能会被重置为活动价
    		        'oprice'         => $value['goods']['price'],
    		        'omarkprice'     => isset($value['goods']['markprice']) ? $value['goods']['markprice'] : $value['goods']['price'],
    		        
    				'goods_image'   => $value['goods']['image'] ? $value['goods']['image'] : '',
    				'source_id'     => $value['source_id'], //??
    		        'type' 		    => $value['type'],
    				//'cst_cate'      =>  $value['cst_cate'],   //或许印花衬衣有
    		        'cloth'         =>  $value['cloth'],   //品类  是取的diy的自定义品类
    		        
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
    		);
    		$hasPrice += intval($price);
    		
//     		if($value['first']){
//     		    //if($value['first'] == '0001' && $value['cloth'] == '0003') continue;
    		    
//     		    $firstData[] = array(
//     		            'user_id' => $_order['user']['user_id'],
//     		            'cloth'   => $value['first'],
//     		            'fabric'  => $value['fabric'],
//     		            'order_id' => $order_id,
//     		    );
//     		}
    		
    		//-- 减库存
    		
    		## 需要减库存
    		    
    		$pInfo = $value['params']['oProducts'];
    		if ($value['type'] != 'fdiy')
    		{
    		    $stock[$pInfo['product_id']] = isset($stock[$pInfo['product_id']]) ? $stock[$pInfo['product_id']] + $value['quantity'] : $value['quantity'];
    		}
    		
    	}
    	
    	$hasPrice = 0;
    	$res = $this->_mod_ordergoods->add(addslashes_deep($goods_items)); //防止二次注入
    	
    	if (!$res){
    		$this->_error('订单商品数据错误!');
    		return false;
    	}
    	
//     	if($firstData){
//     	    $mOfl = &m('orderfirstlog');
//     	    $res = $mOfl->add(addslashes_deep($firstData));
//     	}

//     	if($this->_suit_info){
//     	    $mOsuit = &m('ordersuit');
//     	    foreach ($this->_suit_info as $key=>$row){
//     	        $suits[$key] = $row;
//     	        $suits[$key]['order_id'] = $order_id;
//     	    }
    	    
//         	$res = $mOsuit->add(addslashes_deep($suits));
            
//         	if (!$res){
//         		$this->_error('套装信息有误!');
//         		return false;
//         	}
//     	}
    	
    	//-- 减库存  有点循环更新有点蛋疼啊
//     	$mFabirc = &m('fabric');
//     	foreach ($stc as $key=>$val){
//             $mFabirc->setDec(" CODE = '{$key}'",'STOCK',$val);
//     	}
    	
    	if ($stock){
    	   //减库存
        	$mFabirc = &m('products');
        	 
        	foreach ((array)$stock as $key=>$val){
        	    $res = $mFabirc->setDec(" product_id = '{$key}'",'store',$val);
        	    if($res <= 0){
        	        $this->_error('订单提交失败！错误码：004003101'.$key);
        	        return false;
        	    }
        	}
    	}
    	
    	//抵扣券
    	if($_order['debit']['data']){
    	    foreach ($_order['debit']['data'] as $row){
    	        $debtis[] = array(
                        'order_id' => $order_id,
                        'd_id'     => $row['id'],
                        'd_sn'     => $row['debit_sn'],
                        'd_name'   => $row['debit_name'],
                        'd_money'  => $row['money'],
    	                'd_cloth'  => $row['cate'],
    	        );
    	    }
    	    $mOdebit = &m('orderdebit');
    	    $res = $mOdebit->add(addslashes_deep($debtis));
    	    
    	    if (!$res){
    	        $this->_error('抵扣券使用失败!');
    	        return false;
    	    }
    	}
    	
    	return array('order_id' => $order_id, 'order_sn' => $baseinfo['order_sn'],'status' => $baseinfo['status']);
    }
    
    /**
     * 格式化要入库到order_goods表的数据
     *
     * @author Ruesin
     */
    function _format_order_goods($objs = array()){
        $suit = array();
        $goodsAmount = 0;
        
        foreach ($objs as $oKey=>$oRow){
            if($oRow['list']){
                foreach ($oRow['list'] as $oArr){
                    $prices[$oKey] = ( isset($prices[$oKey]) && intval($prices[$oKey])>0 ) ? ($prices[$oKey]+$oArr['goods']['price']) : $oArr['goods']['price'];
                }
            }else{
                $prices[$oKey] = $oRow['goods']['price']+0;
            }
        }
        foreach ((array)$objs as $key=>$row){
           
            //if($row['type'] == 'suit'){
            if($row['list']){
                $countList = 0;
                $countList = count($row['list']);
                $prcN = $hasMkt = $mktPrice = $hasPrc = $price = 0;
                foreach ($row['list'] as $arr){
                    
                    $prcN += 1;
                    if (isset($prices[$key])){
                        @$mktPrice = $row['goods']['nMarkprice']*($arr['goods']['price']/$prices[$key]);
                        //$price    = $row['goods']['nPrice']*($arr['goods']['price']/$prices[$key]);
                        @$price    = $row['goods']['price']*($arr['goods']['price']/$prices[$key]);
                    }
                    
                    
                    $arr['newPrice']['markprice'] = ( $prcN == $countList) ? $row['goods']['nMarkprice'] - $hasMkt : intval($mktPrice);
                    //$arr['newPrice']['price']     = ( $prcN == $countList) ? $row['goods']['nPrice'] - $hasPrc : intval($price);
                    $arr['newPrice']['price']     = ( $prcN == $countList) ? $row['goods']['price'] - $hasPrc : intval($price);
                    $arr['newPrice']['subtotal']  = $arr['newPrice']['price']*$arr['quantity'];
                    
                    $result[$arr['ident']] = $arr;
                    $row['activity'] = $arr['activity'] ? $arr['activity'] : 0 ;
                    //$goodsAmount += $arr['goods']['price'];
                    $goodsAmount += $arr['subtotal'];
                    
                    $hasMkt += intval($mktPrice);
                    $hasPrc += intval($price);
                }
                
                if($row['type'] == 'suit'){
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
            }else{
                
                //$row['markprice'] = intval($price[$key]);
                $row['newPrice']['markprice'] = intval($row['goods']['nMarkprice']);
                //$row['newPrice']['price']     = intval($row['goods']['nPrice']);
                $row['newPrice']['price']     = intval($row['goods']['price']);
                $row['newPrice']['subtotal']  = $row['newPrice']['price']*$row['quantity'];
                
                $result[$row['ident']] = $row;
                //$goodsAmount += $row['goods']['price'];
                $goodsAmount += $row['subtotal'];
            }
        }
        
        $this->goodsAmount = $goodsAmount;
        
        $this->_suit_info = $suit;
        return $result;
    }
    
    
    /**
     * 处理订单基本信息，返回有效的订单信息数组
     *
     * @author Ruesin
     */
    function _handle_order_info($order, $cart)
    {
        if(!isset($order['invoice']) || empty($order['invoice']) || intval($order['invoice']['type']) <= 0 ){
            $tax['need'] = 0;
        }else{
            $tax['need']  = $order['invoice'][$order['invoice']['type']]['need'];
            $tax['type']  = $order['invoice'][$order['invoice']['type']]['type'];
            $tax['title'] = $order['invoice'][$order['invoice']['type']]['title'];
            $tax['com']   = $order['invoice'][$order['invoice']['type']]['com'];
        }
        
        if(!isset($_SESSION['_order']['ships']['defship'])){
            $this->_error('请选择配送方式');
            return false;
        }
        

        if($cart['final_amount'] <= 0){
        
//             $order_status = ORDER_ACCEPTED; //已付款
        
            //0元订单自动处理 如后台不需审核 状态为生产中
            if($measure['has_measure'] == 1){
                $order_status = ORDER_WAITFIGURE;   // 待量体
            }else{
                $order_status = ORDER_PRODUCTION;   // 生产中
            }
        
        }else{
            $order_status = ORDER_PENDING; //待付款
        }
        
        
        /* 返回基本信息 */
        return array(
                //预约量体信息,拆出去
                
                'order_sn'       => $this->_gen_order_sn(),
                'extension'      => 'news',
                'discount'       => $order['discount'],
                'goods_amount'   => $cart['goods_amount'],
                'final_amount'   => $cart['final_amount']+$order['ships']['defship']['post_fee']-$order['discount'],//订单最后支付价格 订单价格+物流费用-优惠价格
                'order_amount'   => $cart['order_amount']+$order['ships']['defship']['post_fee'],
                'money_amount'   => $cart['money_amount'],
                'profit_amount'  => $cart['profit_amount'],
                'coin'           => $cart['coin'],
                
                'point_g'        => $cart['point_g'],
                //'cloth'          => '', 
                'user_id'        => $order['user']['user_id'],
                'user_name'      => $order['user']['user_name'],
                'has_measure'    => $measure['has_measure'],
                
                'one_num'        => $order['one_num'],
                'one_id'         => $order['one_id'],
                'one_status'     => 1,
                
                'status'         => $order_status,
                'add_time'       => gmtime(),
                //'finished_time'  => '',
                'last_modified' => gmtime(),
                
                //是否上门量体
                'measure'     => $measure['type'],
                
                'is_gift'     => $cart['is_gift'] ? 1 : 0,
                
                //支付
                'payment_id'     => $order['payment']['payment_id'],
                'payment_name'   => $order['payment']['payment_name'],
                'payment_code'   => $order['payment']['payment_code'],
                //'out_trade_sn'   => '',
                'pay_time' => $order_status != ORDER_PENDING ? gmtime() : '',
                //'pay_message' => '',
                //'cost_payment'  => $cart['final_amount'],  //订单支付金额
                
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
//                 'ship_area'      => $ship['address']['region_name'],
//                 'ship_area_id'      => $ship['address']['region_id'],
//                 'ship_addr'      => $ship['address']['address'],
//                 'ship_name'      => $ship['address']['consignee'] ? $ship['address']['consignee'] : $ship['store']['ship_name'],
//                 'ship_zip'       => $ship['address']['zipcode'],
//                 'ship_mobile'    => $ship['address']['phone_mob'] ? $ship['address']['phone_mob']  : $ship['store']['ship_mob'],
//                 'ship_tel'       => $ship['address']['phone_tel'],
//                 'ship_email'     => $ship['address']['email'],
//                 //'ship_time'      => $order['address']['ship_time'],
                
                //配送
                'shipping_id'    => $order['ships']['defship']['shipping_id'],
                'measure_fee'    => $order['ships']['defship']['post_fee'],    //物流运费
                'shipping'       => $ship['shipping'],
                //'cost_freight'   => 0,//免邮
                //'weight'         => $cart['weight'],
                'ship_store'     => $ship['store']['server_id'],//自提门店
                
                //发票
                'invoice_need'   => $tax['need'],
                'invoice_type'   => $tax['type'],
                'invoice_title'  => $tax['title'],
                'invoice_com'    => $tax['com'],
                
                'memo'          => $order['remark'],
                //'ip'            => $order['source']['ip'] ,
                'source_from'   => $this->_source_from,
                
        );
    }
    
    /**
     * 获取量体数据
     * 
     * @author Ruesin
     */
    function _handle_figure_info($order,$cart){
        
        if(!$order['measure']){ //$cart['sizehasdiy'] != 'yes' ||
            return false;
        }
        $son_sn = $this->_gen_son_sn();
        if($order['measure']['type'] == 1 || $order['measure']['type'] == 2  || $order['measure']['type'] == 6){
            $data = $order['measure']['data'][$order['measure']['type']];
            //取地区先放在这里  其实是可以放在操作过程中的
            $mRe = &m('region');
            $area  = $mRe->get("region_id = '{$data['region_id']}'");
            $figure = array(
                    'son_sn'    => $son_sn,
                    'measure'   => $order['measure']['type'],
                    'realname'  => $data['realname'],
                    'phone'     => $data['phone'],
                    'gender'    => $data['gender'] ? $data['gender'] : '10040',
                    'area'      => $area['region_name'],
                    'addr'      => $data['addr'],
                    'time'      => $data['time'],
                    'time_noon' => $data['time_noon'],
                    'server_id' => $data['server_id'],
                    'userid'    => $order['user']['user_id'],
            );
            

            //创业者顾客表数据
            $this->mfData = array(
                'son_sn'          => $son_sn,
            	'figure_state'    => '0',
            	'storeid'         => $order['user']['user_id'],
            	'customer_name'   => $data['realname'],
            	'customer_mobile' => $data['phone'],
                'firsttime'       => gmtime(),
                'lasttime'        => gmtime(),
                'gender'          => $data['gender'],
                'service_mode'    => $order['measure']['type'],
            );
            
            if($order['measure']['type'] == 6){
                $figure['liangti_id'] = $data['liangti'];
                $figure['liangti_name'] = $data['liangti_name'];
                
                $this->mfData['liangti_id'] = $data['liangti'];
                $this->mfData['liangti_name'] = $data['liangti_name'];
                $this->mfData['id_serve'] = $data['server_id'];
                
                
            }
            
            return $figure;
        }

        //历史数据这个不合理啊，如果历史数据要新增写入量体表的话，同一个量体数据会无限加的。//先这么做吧  稍后改到订单中做判断
        if($order['measure']['type'] == 3){
            $data = $order['measure']['data'][3];
            $oData = $this->_mod_orderfigure->get($data['history_id']);
            unset($oData['id']);
            unset($oData['order_id']);
            $oData['modi_time'] = gmtime();
            return $oData;
        }
        
        if($order['measure']['type'] == 4){
            //算法暂定
            
            
        }
        
        //选择现有量体数据  //创业者顾客表  customer_figure  //这块的数据对应后续肯定会继续有问题
        if($order['measure']['type'] == 5){
            $mCf  = &m('customer_figure');
            $history = $mCf->get("figure_sn = '{$order['measure']['data'][5]['history_id']}'");
            
            $figure  = array(
                    'son_sn'      => $son_sn,
                    'measure'     => $order['measure']['type'],
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
                    'userid'       => $order['user']['user_id'],  //$history['storeid'],
                    'figure_state' => $history['figure_state'],
                    'modi_time'    => $history['lasttime'],
            );
            
            $cloudData = file_get_contents('http://api.figure.mfd.cn/soap/figure.php?act=getFields');
            //$cloudData = file_get_contents('http://api.figure.dev.mfd.cn:8080/soap/club.php?act=getFields');
            
            $jFlag = 0;
            if(isset($cloudData)){
                $fData = json_decode($cloudData,1);
                if($fData && isset($fData['result']['data'])){
                    $jFlag = 1;
                }
            }
            if($jFlag){
                $cloth = $cart['check_cloth'];
                foreach ($fData['result']['data'] as $key=>$row){
                    if(isset($row['isshow']) && $row['isshow'] == 1){
                        if($key == 'lheight' || $key == 'lweight'){
                            $history[$key] = $history[str_replace('l', '', $key)];
                        }
                        if(intval($history[$key]) <= 0){
                            if (isset($row['cate'])){
                                foreach ($row['cate'] as $k=>$v){
                                    if (isset($cloth[$k])){
                                        $this->_error((isset($row['zname']) ? $row['zname'] : '').' - 已有量体数据有误!');
                                        return false;
                                    }
                                }
                            }else{
                                $this->_error((isset($row['zname']) ? $row['zname'] : '').' - 已有量体数据有误!');
                                return false;
                            }
                            
                        }
                    }
                    $figure[$key] = $history[$key];
                }
            }else{
                $mMf = &m('member_figure');
                foreach ($mMf->_positions() as $key=>$val){
                    $figure[$key] = $history[$key];
                }
                
                $figure['body_type_19'] = $history['body_type_19'];
                $figure['body_type_20'] = $history['body_type_20'];
                $figure['body_type_24'] = $history['body_type_25'];
                $figure['body_type_26'] = $history['body_type_26'];
                $figure['body_type_3']  = $history['body_type_3'];
                $figure['body_type_2000'] = $history['body_type_2000'];
                $figure['body_type_3000'] = $history['body_type_3000'];
                $figure['body_type_4000'] = $history['body_type_4000'];
                $figure['body_type_6000'] = $history['body_type_6000'];
                $figure['body_type_90000'] = $history['body_type_90000'];
                $figure['styleLength'] = $history['styleLength'];
                $figure['styleDY'] = $history['styleDY'];
                $figure['height'] = $history['height'];
                $figure['weight'] = $history['weight'];
                $figure['lheight'] = $history['height'];
                $figure['lweight'] = $history['weight'];
                $figure['unit'] = $history['unit'];
                $figure['part_label_10130'] = $history['part_label_10130'];
                $figure['part_label_10131'] = $history['part_label_10131'];
            }
            
            return $figure;
        }
        
        if($order['measure']['type'] == 6){
            
        }
    }
    
    /**
     * 响应支付成功 修改订单状态
     * 
     * @author Ruesin
     */
    function respond_notifyssss($order_id, $notify_result)
    {
        $where = "order_id = '{$order_id}'";
        $data = array('status' => $notify_result['target']);
        //$data = array('status' => STORE_ACCEPTED);
    
        switch ($notify_result['target'])
        {
        	case ORDER_ACCEPTED:
        	    $where .= ' AND status=' . ORDER_PENDING;  //代付款状态
        	    $data['status'] = ORDER_CHECKING;       //状态直接进入审核中
    
        	    $data['pay_time']   =   gmtime();
    
        	    break;
        	case ORDER_SHIPPED:
        	    $where .= ' AND status=' . ORDER_ACCEPTED;  //只有等待发货的订单才会被修改为已发货
        	    $data['ship_time']  =   gmtime();
        	    break;
        	case ORDER_FINISHED:
        	    $where .= ' AND status=' . STORE_SHIPPED;   //只有 品牌商 已发货的订单才会被自动修改为交易完成
        	    $data['finished_time'] = gmtime();
        	    break;
        	case ORDER_CANCLED:                             //任何情况下都可以关闭
        	    /* 加回商品库存 待定状态 */
        	    // $model_order->change_stock('+', $order_id);
        	    break;
        }
    
        return $this->_mod_order->edit($where, $data);
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
            /* mt_srand((double) microtime() * 1000000);
            $timestamp = gmtime();
            $y = date('y', $timestamp);
            $z = date('z', $timestamp);
            $order_sn = 'SN'.$y . str_pad($z, 3, '0', STR_PAD_LEFT) . mt_rand(10000, 99999) .str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT); */
            //由于推送时只接收12位订单号，更改
            /* mt_srand((double) microtime() * 1000000);
            $timestamp = gmtime();
            $y = date('y', $timestamp);
            $z = date('z', $timestamp);
            $order_sn = $y . str_pad($z, 3, '0', STR_PAD_LEFT) . mt_rand(10000, 99999) .str_pad(mt_rand(10, 99), 2, '0', STR_PAD_LEFT); */
            //$order_sn = date('Ymd', $timestamp) . mt_rand(1000, 9999);
            $order_sn = date('Ymd', $timestamp) . str_pad(mt_rand(0,9999), 4, '0', STR_PAD_LEFT);
        }while ($this->_mod_order->find("order_sn='{$order_sn}'"));
    
        return $order_sn;
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


