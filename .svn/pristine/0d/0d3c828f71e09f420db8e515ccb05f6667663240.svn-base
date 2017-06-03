<?php
require 'edblib.php';
require  './eccore/model/model.base.php';
 
//include_once 'order_func.php';
// initDb();
require 'vendor/autoload.php';
require  './includes/functions.lib.php';
class EdbDemo
{
		//获取订单
	public function edbTradeGet($num=0,$sizes=0){
	
		if($num==0){
			 $num=1;
		}
	    if($sizes==0){
			$sizes=20;
		}
		
		  //获取当天的年份
	  //  $y = date("Y");
	    
	    //获取当天的月份
	   // $m = date("m");
	    
	    //获取当天的号数
	   // $d = date("d");
	    //$todayTimes= mktime(0,0,0,$m,$d,$y);
	    //$todayTime=date("Y-m-d H:i:s",$todayTimes);
		$todays=date("Y-m-d",strtotime("-1 day"));
	    $today=date("Y-m-d H:i:s",strtotime($todays));
	    $toms=date("Y-m-d",strtotime("+1 day"));
	    $tom=date("Y-m-d H:i:s",strtotime($toms));
	    $cl=new EdbLib();
	    $params = $cl->edbGetCommonParams('edbTradeGet');
		
	    $params['date_type'] = '发货日期';
		$params['order_status'] = '已发货';
	    $params['begin_time'] = $today;
	   // $params['begin_time'] = '2017-04-20 00:00:00';
		$params['shopid'] = '27';
		$params['merge_status'] = ' ';

		$params['fields'] = 'out_tid,express_no,express,expressNo,merge_status,tid';
	   $params['end_time'] = $tom;
	   // $params['end_time'] = '2017-04-21 0:00:00';
	    $params['page_size'] = $sizes;
	    $params['page_no'] = $num;
	    $params['productInfo_type'] = '1';
	
	    $res=$cl->edbRequstPost($params);
	  
	    //订单返回物流
	    $results=json_decode($res);
	   
	    $result=object_array($results);
	   
	    $order_info=$result['Success']['items']['item'];
			
		$order_count=count($order_info);
	
	    //判断有误单号
	   // 
		 
	  
	    $order=m('order');
	    $shipping=&m('shipping');
		
	    if($order_info){	
	       
	        foreach($order_info as $key=>$val){
	          
	            $sql_order = "select * from cf_order where order_sn={$val['out_tid']} AND waybillno is null  ";
	        		  $orderss = mysql_query($sql_order);
	        	
					   $orders   = mysql_fetch_assoc($orderss);
					 
	        		  if($orders){
	        		      //判断物流
	        		      mysql_query("SET NAMES UTF8");
						  $sql_ship = "select * from cf_shipping where shipping_name='{$val['express']}'  ";
	        		      $ships = mysql_query($sql_ship);
					      $ship   = mysql_fetch_assoc($ships);
						  $shippings_id=$ship['shipping_id']?$ship['shipping_id']:0;
	        		      //写入物流单号
	        		      $usql = "UPDATE cf_order SET status=30,ship_time=".time().",waybillno=".$val['express_no'].",shipping_id=".$shippings_id." WHERE order_id = ".$orders['order_id'];
	        		      
						 $res= mysql_query($usql);
                         //如果订单为合并订单
						
                         if($val['merge_status']){
                            $outsn='';
							 
                             if($val['tid_item']){
								 
                                 foreach($val['tid_item'] as $k1=>$v1){
                                     $outsn[]=$v1['out_tid'];
                                     $sql_order1 = "select * from cf_order where order_sn={$v1['out_tid']} AND waybillno is null  ";
                                     $orderss1 = mysql_query($sql_order1);
                                     $orders1   = mysql_fetch_assoc($orderss1);
                                     if($orders1){
                                         //写入物流单号
                                         $usql1 = "UPDATE cf_order SET status=30,ship_time=".time().",waybillno=".$val['express_no'].",shipping_id=".$shippings_id." WHERE order_sn = ".$v1['out_tid'];
                                          
                                          $res1= mysql_query($usql1);
                                 }
                                 
                             }
                         }
                         }
						
	        		      if(!$res){							 
	        		          echo '物流更新失败';
	        		      }else{
							   //发货通知
							  if($val['merge_status']){
								 $outsn1= array_unique($outsn);
								  $outsns=implode(',',$outsn1);
								 
								fahuo($orders['order_id'],$val['express_no'],1,$outsns);   
							  }else{
                                fahuo($orders['order_id'],$val['express_no']);  
							  }
							 
						  }
	        		  }else{
	        		      echo '暂无匹配订单';
	        		  }
	        }
			 if($order_count = $sizes){

			$this->getedb($num+1,$sizes);
			
		}
	    }else{
			echo '无订单';die;
		}
	   
	}

	public function getedb($num,$sizes){

       $this->edbTradeGet($num,$sizes);
	}
	//添加订单
	public function edbTradeAdd($order=0,$num=0,$ordergoods='',$region1='',$region2='',$region3=''){
		$cl=new EdbLib();
		$params = $cl->edbGetCommonParams('edbTradeAdd');
		
		/*$time='2017-02-02 18:47:27';
		$xmlValuse='';
		$xmlValuse ='<order>';
		$xmlValuse=$xmlValuse.'<orderInfo>';
		$xmlValuse=$xmlValuse.'<out_tid>201702021413</out_tid>';
		$xmlValuse=$xmlValuse.'<shop_id>27</shop_id>';
		
		$xmlValuse=$xmlValuse.'<buyer_id>697</buyer_id>';
		$xmlValuse=$xmlValuse.'<buyer_msg></buyer_msg>';
		$xmlValuse=$xmlValuse.'<consignee>吴苏</consignee>';
		$xmlValuse=$xmlValuse.'<telephone>15810280954</telephone>';
		$xmlValuse=$xmlValuse.'<mobilPhone>15810280954</mobilPhone>';
		$xmlValuse=$xmlValuse.'<privince>中国</privince>';
		$xmlValuse=$xmlValuse.'<city>北京市</city>';
		$xmlValuse=$xmlValuse.'<area>朝阳</area>';
		$xmlValuse=$xmlValuse.'<address>中国	北京市	朝阳 朝外大街6号新城国际21号楼1003室</address>';
		$xmlValuse=$xmlValuse.'<order_date>'.$time.'</order_date>';
		$xmlValuse=$xmlValuse.'<express>德邦物流</express>';
		$xmlValuse=$xmlValuse.'<pay_status>已付款</pay_status>';
		$xmlValuse=$xmlValuse.'<order_type>已付款</order_type>';
		$xmlValuse=$xmlValuse.'</orderInfo>';
		
		$xmlValuse=$xmlValuse.'<product_info>';
		
		            $xmlValuse=$xmlValuse.'<product_item>';
		            $xmlValuse=$xmlValuse.'<barCode>6958862302555</barCode>';
		            $xmlValuse=$xmlValuse.'<product_title>三文鱼双拼成猫</product_title>';
		            $xmlValuse=$xmlValuse.'<orderGoods_Num>1</orderGoods_Num>';
		            $xmlValuse=$xmlValuse.'<out_tid>201702021413</out_tid>';
		            $xmlValuse=$xmlValuse.'</product_item>';
		            $xmlValuse=$xmlValuse.'<product_item>';
		            $xmlValuse=$xmlValuse.'<barCode>6958862105965</barCode>';
		            $xmlValuse=$xmlValuse.'<product_title>咬胶 清齿倍健</product_title>';
		            $xmlValuse=$xmlValuse.'<orderGoods_Num>1</orderGoods_Num>';
		            $xmlValuse=$xmlValuse.'<out_tid>201702021413</out_tid>';
		            $xmlValuse=$xmlValuse.'</product_item>';
		            $xmlValuse=$xmlValuse.'<product_item>';
		            $xmlValuse=$xmlValuse.'<barCode>6958862104364</barCode>';
		            $xmlValuse=$xmlValuse.'<product_title>湿粮 牛肉加甘薯</product_title>';
		            $xmlValuse=$xmlValuse.'<orderGoods_Num>1</orderGoods_Num>';
		            $xmlValuse=$xmlValuse.'<out_tid>201702021413</out_tid>';
		            $xmlValuse=$xmlValuse.'</product_item>';
		            $xmlValuse=$xmlValuse.'<product_item>';
		            $xmlValuse=$xmlValuse.'<barCode>6958862101974</barCode>';
		            $xmlValuse=$xmlValuse.'<product_title>湿粮 补钙壮骨</product_title>';
		            $xmlValuse=$xmlValuse.'<orderGoods_Num>1</orderGoods_Num>';
		            $xmlValuse=$xmlValuse.'<out_tid>201702021413</out_tid>';
		            $xmlValuse=$xmlValuse.'</product_item>';
		
		
		$xmlValuse=$xmlValuse.'</product_info>';
		$xmlValuse=$xmlValuse.'</order>';
		*/
	
		
        $time=date("Y-m-d H:i:s",$order['add_time']);
        $xmlValuse='';
		$xmlValuse ='<order>';
		$xmlValuse=$xmlValuse.'<orderInfo>';
		$xmlValuse=$xmlValuse.'<out_tid>'.$order['order_sn'].'</out_tid>';
		$xmlValuse=$xmlValuse.'<shop_id>27</shop_id>';

		$xmlValuse=$xmlValuse.'<buyer_id>'.$order['user_id'].'</buyer_id>';
		$xmlValuse=$xmlValuse.'<buyer_msg>'.$order['memo'].'</buyer_msg>';
		$xmlValuse=$xmlValuse.'<consignee>'.$order['ship_name'].'</consignee>';
		$xmlValuse=$xmlValuse.'<telephone>'.$order['ship_mobile'].'</telephone>';
		$xmlValuse=$xmlValuse.'<mobilPhone>'.$order['ship_mobile'].'</mobilPhone>';
		$xmlValuse=$xmlValuse.'<privince>'.$region1.'</privince>';
		$xmlValuse=$xmlValuse.'<city>'.$region2.'</city>';
		$xmlValuse=$xmlValuse.'<area>'.$region3.'</area>';
		$xmlValuse=$xmlValuse.'<address>'.$order['ship_area'].$order['ship_addr'].'</address>';
		$xmlValuse=$xmlValuse.'<order_date>'.$time.'</order_date>';
		$xmlValuse=$xmlValuse.'<express>德邦物流</express>';
		$xmlValuse=$xmlValuse.'<pay_status>已付款</pay_status>';
		$xmlValuse=$xmlValuse.'<order_type>已付款</order_type>';
		$xmlValuse=$xmlValuse.'</orderInfo>';

		$xmlValuse=$xmlValuse.'<product_info>';
	
		if($ordergoods){
			   foreach($ordergoods as $key=>$val){
                   if($val['type'] != 'fdiy'){
					   $xmlValuse=$xmlValuse.'<product_item>';
                      $xmlValuse=$xmlValuse.'<barCode>'.$val['goodsn'].'</barCode>';
			          $xmlValuse=$xmlValuse.'<product_title>'.$val['goods_name'].'</product_title>';	
			          $xmlValuse=$xmlValuse.'<orderGoods_Num>'.$val['quantity'].'</orderGoods_Num>';
			          $xmlValuse=$xmlValuse.'<out_tid>'.$order['order_sn'].'</out_tid>';
					  $xmlValuse=$xmlValuse.'</product_item>';
                   }
               }
		}

		
		$xmlValuse=$xmlValuse.'</product_info>';
		$xmlValuse=$xmlValuse.'</order>';
		
		$params['xmlValues']=$xmlValuse;
		$res=$cl->edbRequstPost($params);
		return $res;
	}
	//更新物流信息
	public function edbLogisticsCompaniesUpdate(){
		$cl=new EdbLib();
		$params = $cl->edbGetCommonParams('edbLogisticsCompaniesUpdate');
		$params['tid'] = 'S1512020000001';//E店宝订单号
		$params['express'] = '圆通';
		$params['express_deliveryno'] = '809173667727';
		$params['delivery_time'] = '2016-11-13 18:20:10';
		$params['weight'] = '0';
		 
		$res=$cl->edbRequstPost($params);
	}
}
?>