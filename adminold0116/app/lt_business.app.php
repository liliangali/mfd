<?php
/* BD业务统计
 * 2015-12-14 by shao
 *  */
class lt_businessApp  extends BackendApp
{
	var $_member_mod;
	var $_ordergoods;
	var $_orderfigure;
	var $_serve_mod;
	var $_order_mod;
	function __construct(){
		$this->_member_mod = &m("member");
		$this->_ordergoods = &m("ordergoods");
		$this->_orderfigure = &m("orderfigure");
		$this->_serve_mod = &m("serve");
		$this->_order_mod = & m('order');
           parent::__construct();
    }


    //列表
    function index(){
    	//订单号	下单时间	下单账号	订单产品	订单金额	被量体人	量体数据来源	量体师姓名	量体师归属	量体费	是否第一次使用该量体数据	是否返修/退货
    	//order 订单表   订单号	下单时间	下单账号   订单金额
    	//order_goods  订单产品
    	//order_figure
    	//member
    	//serve
    	//customer_figure
    	//figure_order_m
    	$order_mtm_log = & m('ordermtmlogs');
    	
    	$figure_orderm=m("figureorderm");
    	$customer_figure=m("customer_figure");
    	$conditions='';
    	 if($_GET['add_time_from']){
    		$addtimef=strtotime($_GET['add_time_from']);
    	   $conditions.=" AND order_alias.add_time >='{$addtimef}'";
    	}
    	if($_GET['add_time_to']){
    		$addtimet=strtotime($_GET['add_time_to']);
    		$conditions.=" AND order_alias.add_time <='{$addtimet}'";
    	}
    	if($_GET['add_time_go'] && $_GET['add_time_do'])
    	{
    		$addtimeg=strtotime($_GET['add_time_go']);
    		$addtimed=strtotime($_GET['add_time_do']);
    		if($addtimeg>$addtimed){
    			$this->show_message("搜索起始时间不能大于终止时间");
    			return false;
    		}
    		$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >= '{$addtimeg}' AND delivery_date <= '{$addtimed}' ORDER BY delivery_date DESC) AS T
    		GROUP BY T.order_id";
    		$db = &db();
    		$sql_c=$db->query($sql_count);
    		$mtm_timey = array();
    		while($rows=@mysql_fetch_assoc($sql_c)){
    		$mtm_timey[]=$rows;
    		}
    		/* $mtm_timey = $order_mtm_log->find(array(
    				'conditions'=>"delivery_date >= '{$addtimeg}' AND delivery_date <= '{$addtimed}'",
    		)); */
    		
    		$mtm_timeys=i_array_column($mtm_timey,'order_id');
    		$generaliyy=db_create_in($mtm_timeys,'order_figure.order_id');
    		$conditions.=" AND ".$generaliyy;
    	}else{
    		 if($_GET['add_time_go']){
    			$addtimeg1=strtotime($_GET['add_time_go']);
    			$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >= '{$addtimeg}' AND delivery_date <= '{$addtimed}' ORDER BY delivery_date DESC) AS T
    			GROUP BY T.order_id";
    			$db = &db();
    			$sql_c=$db->query($sql_count);
    			$mtm_time1 = array();
    			while($rows=@mysql_fetch_assoc($sql_c)){
    			$mtm_time1[]=$rows;
    			}
    			/* $mtm_time1 = $order_mtm_log->find(array(
    					'conditions'=>"delivery_date >='{$addtimeg1}' AND delivery_date <> 0",
    			)); */
    			$mtm_times=i_array_column($mtm_time1,'order_id');
    			$generaliys1=db_create_in($mtm_times,'order_figure.order_id');
    			$conditions.=" AND ".$generaliys1;
    		}elseif($_GET['add_time_do']){
    			$addtimed=strtotime($_GET['add_time_do']);
    			$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >= '{$addtimeg}' AND delivery_date <= '{$addtimed}' ORDER BY delivery_date DESC) AS T
    			GROUP BY T.order_id";
    			$db = &db();
    			$sql_c=$db->query($sql_count);
    			$mtm_time2 = array();
    			while($rows=@mysql_fetch_assoc($sql_c)){
    			$mtm_time2[]=$rows;
    			}
    			/* $mtm_time2 = $order_mtm_log->find(array(
    					'conditions'=>"delivery_date <='{$addtimed}' AND delivery_date <> 0",
    			)); */
    			$mtm_times2=i_array_column($mtm_time2,'order_id');
    			$generaliys2=db_create_in($mtm_times2,'order_figure.order_id');
    			$conditions.=" AND ".$generaliys2;
    		} 
    	}
    	
    	if($addtimef && $addtimet){
    		if($addtimef>$addtimet){
    			$this->show_message("搜索起始时间不能大于终止时间");
    			return false;
    		}
    	}
    	

    	if($_GET['field'] !='all' && $_GET['field'] && $_GET['search_name'])
    	{
    		if($_GET['field'] == 'glize')
    		{
    			$serve=m("serve");
    			$serves=$serve->find(array(
    					'conditions' =>"serve_name LIKE '%{$_GET['search_name']}%' ",
    			));
    			$generaliy=i_array_column($serves,'idserve');
    		    $generaliys=db_create_in($generaliy,'order_figure.server_id');
    			$conditions.=" AND ".$generaliys;
    		}elseif($_GET['field'] == 'liangti_name'){
    			$conditions.=" AND order_figure.liangti_name LIKE '%{$_GET['search_name']}%'";
    		}
    	}
    	/* if($_GET['search_name'])
    	{
    			$conditions.=" AND order_figure.liangti_name LIKE '%{$_GET['search_name']}%'";
    	} */
    	$serves=$this->_serve_mod->find(array(
    			'conditions' =>"1=1",
    			'fields' =>"idserve,userid,linkman,serve_name,card_number",
    			'index_key'=>"idserve",
    	));
    	if($serves){
    		foreach($serves as $sk=>$sv)
    		{
    			$servels[$sv['idserve']]=$sv['serve_name'];
    		}
    	}
    	$this->assign('serves',$servels);
    	$ordercos= $this->_orderfigure->find(array(
    			'conditions' =>"order_alias.extension= 'news' AND order_alias.status in (30,40)".$conditions,
    			'fields' =>"id",
    			'join'=>'has_order',
    	));
    	$page = $this->_get_page(30);
    	$orders= $this->_orderfigure->find(array(
    			'conditions' =>"order_alias.extension= 'news' AND order_alias.status in (30,40)".$conditions,
    			'fields' =>"id,order_figure.liangti_id,order_figure.history_id,order_figure.son_sn,order_figure.server_id,order_figure.liangti_name,order_figure.measure,order_figure.realname,order_alias.order_sn,order_alias.final_amount,order_alias.money_amount,order_alias.coin,order_alias.order_amount,order_alias.user_id,order_alias.add_time,order_alias.user_name,order_alias.status,order_alias.ship_time,order_alias.r_order_id,order_alias.order_id",
    			'limit' => $page['limit'],
    			'join'=>'has_order',
    			'order'=>"id DESC",
    	));
    	if($orders){
    		foreach($orders as $key=>$val)
    		{
    			$cloth='';
    			if($val['liangti_id']){
    				$figure_liangti=m("figure_liangti");
    				$card_numbers=$figure_liangti->get(array(
    						'conditions' =>"liangti_id = '{$val['liangti_id']}'",
    						'join'=>'has_member',
    						'fields' =>"card_number,real_name",
    				));
    				$card_number=$card_numbers['card_number'];
    				if(!$val['liangti_name']){
    					$liangti_name=$card_numbers['real_name'];
    				}else{
    					$liangti_name=$val['liangti_name'];
    				}
    			}else{
    				if($val['server_id']){
    					$card_number=$serves[$val['server_id']]['card_number'];
    				}
    				if(!$val['liangti_name']){
    					$liangti_name=$serves[$val['server_id']]['linkman'];
    				}else{
    					$liangti_name=$val['liangti_name'];
    				}
    			}
    			$ordergoods=$this->_ordergoods->find(array(
    					'conditions' =>"order_id='{$val['order_id']}' group by goods_name having sum(quantity)",
    					'fields'=>"quantity,goods_name,sum(quantity) as squantity",
    			));
    			
    			if($ordergoods){
    				
    				foreach($ordergoods as $ok=>$or){
    					$cloth .=$or['squantity']."件".$or['goods_name'];
    				}
    			}
    			if($val['measure'] ==5){
    				$customs=$customer_figure->get(array(
    						'conditions' =>"figure_sn='{$val['history_id']}'",
    						'fields'     =>"service_mode",
    				));
    				if($customs['service_mode'] == 3){
    					$orfigus= $this->_orderfigure->get(array(
    							'conditions' =>"id < '{$val['id']}' AND history_id = '{$val['history_id']}'",
    					));
    					if($orfigus){
    						$is_history='否';
    					}else{
    						$is_history='是';
    					}
    					$measure='线下采集';
    				}else{
    					if($customs['service_mode'] ==1){
    						$measure='上门';
    					}elseif($customs['service_mode'] ==2){
    						$measure='门店';
    					}elseif($customs['service_mode'] ==6){
    						$measure='指派量体师';
    					}elseif($customs['service_mode'] ==4){
    						$measure='后台录入';
    					}
    					$is_history='否';
    				}
    			}else{
    				$is_history='是';
    				if($val['measure'] ==1){
    					$measure='上门';
    				}elseif($val['measure'] ==2){
    					$measure='门店';
    				}elseif($val['measure'] ==6){
    					$measure='指派量体师';
    				}
    			}
    			 if($val['son_sn']){
    				$figure_ors=$figure_orderm->get(array(
    						'conditions' =>"son_sn='{$val['son_sn']}'",
    						'fields' =>"modi_time",
    				));
    				if($figure_ors['modi_time']){
    					$modi_time=date("Y-m-d H:i:s",$figure_ors['modi_time']);
    				}else{
    					/*  $fi_ors=$figure_orderm->get(array(
    							'conditions' =>"order_id='{$val['order_id']}'",
    							'fields' =>"modi_time",
    					));
    					if($fi_ors['modi_time']){
    						$modi_time=date("Y-m-d H:i:s",$fi_ors['modi_time']);
    					}else{  */
    						$modi_time='';
    					/*  }  */
    					
    				}
    				
    			}else{
    				$figure_ors=$figure_orderm->get(array(
    						'conditions' =>"order_id='{$val['order_id']}'",
    						'fields' =>"modi_time",
    				));
    				if($figure_ors['modi_time']){
    					$modi_time=date("Y-m-d H:i:s",$figure_ors['modi_time']);
    				}else{
    					$modi_time='';
    				}
    			}
    			$money=$val['final_amount']+$val['money_amount']+$val['coin'];
    			if($is_history == '否'){
    				$business=0;
    			}else{
    			if($val['order_amount']*0.3 ==$money){
    			  $business="0（工装）";
    		    }/* elseif($val['order_amount']*0.3 > $money){
    			 $business="0（测试）";
    		    } */else{ 
    		    		$business=100;
    		     }  	
    			}
    			$mtm_time = $order_mtm_log->get(array(
    					'conditions'=>"order_id ='{$val['order_id']}'",
    					'order'=>"delivery_date DESC",
    			));
    			
    			//$mtm_time = $order_mtm_log->get("order_id ='{$val['order_id']}'");
    			if($mtm_time){
    				$ship_time= date("Y-m-d H:i",$mtm_time['delivery_date']);
    			}else{
    			$ship_time='';
    		    }		
    		    $mtm_ids =array_filter(explode(',', $val['r_order_id']));
    		   
    			$orders[$key]['add_time']=date("Y-m-d H:i:s",$val['add_time']);
    			$orders[$key]['cloth']=$cloth;
    			$orders[$key]['measure']=$measure;
    			$orders[$key]['is_history']=$is_history;
    			$orders[$key]['money']=$money;
    			$orders[$key]['card_number']=$card_number;
    			$orders[$key]['business']=$business;
    			$orders[$key]['liangti_name']=$liangti_name;
    			$orders[$key]['r_order_id']=$mtm_ids;
    			$orders[$key]['ship_time']=$ship_time;
    			$orders[$key]['modi_time']=$modi_time;
    		}
    	}
    	$page['item_count'] = count($ordercos);
    	$this->_format_page($page);
    	$this->assign('page_info', $page);
    	$this->assign('order',$orders);
    	$this->assign('status',array(
    			'11' => '待付款', //11 待付款
    			'12'=> '待量体', //12 待量体
    			'20' => '已支付', //20 已支付
    			'60'=> '生产中', //60 //生产中
    			'61'=> '备货中',   // 61
    			'30'=> '已发货', //30
    			'41' => '返修中', //41
    			'40' => '已完成', //40
    			'70' => '退款中',//70
    			'80' => '已退款',//70
    			'0'=> '已取消', //0
    			'43' => '订单异常', //43 订单异常(订单推送异常)
    			'44' => '物流异常', //44 订单异常(物流推送异常)
    	));
    	$this->assign('selects',array(
    			//'all'=>'全部',
    			'glize'   => '门店名称',
    			'liangti_name'   => '量体师名',
    	));
    
    	$this->assign('query',$_GET);
        $this->display('lt_business/lt_business.index.html');
    }
    
    function export(){
    	$order_mtm_log = & m('ordermtmlogs');
    	$figure_orderm=m("figureorderm");
    	$customer_figure=m("customer_figure");
    	$conditions='';
    	if($_GET['add_time_from']){
    		$addtimef=strtotime($_GET['add_time_from']);
    		$conditions.=" AND order_alias.add_time >='{$addtimef}'";
    	}
    	if($_GET['add_time_to']){
    		$addtimet=strtotime($_GET['add_time_to']);
    		$conditions.=" AND order_alias.add_time <='{$addtimet}'";
    	}
    	/* if($_GET['add_time_go']){
    		$addtimeg=strtotime($_GET['add_time_go']);
    		$conditions.=" AND order_alias.ship_time >='{$addtimeg}'";
    	}
    	if($_GET['add_time_do']){
    		$addtimed=strtotime($_GET['add_time_do']);
    		$conditions.=" AND order_alias.ship_time <='{$addtimed}'";
    	} */
    if($_GET['add_time_go'] && $_GET['add_time_do'])
    	{
    		$addtimeg=strtotime($_GET['add_time_go']);
    		$addtimed=strtotime($_GET['add_time_do']);
    		if($addtimeg>$addtimed){
    			$this->show_message("搜索起始时间不能大于终止时间");
    			return false;
    		}
    		$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >= '{$addtimeg}' AND delivery_date <= '{$addtimed}' ORDER BY delivery_date DESC) AS T
    		GROUP BY T.order_id";
    		$db = &db();
    		$sql_c=$db->query($sql_count);
    		$mtm_timey = array();
    		while($rows=@mysql_fetch_assoc($sql_c)){
    		$mtm_timey[]=$rows;
    		}
    		/* $mtm_timey = $order_mtm_log->find(array(
    				'conditions'=>"delivery_date >= '{$addtimeg}' AND delivery_date <= '{$addtimed}'",
    		)); */
    		
    		$mtm_timeys=i_array_column($mtm_timey,'order_id');
    		$generaliyy=db_create_in($mtm_timeys,'order_figure.order_id');
    		$conditions.=" AND ".$generaliyy;
    	}else{
    		 if($_GET['add_time_go']){
    			$addtimeg1=strtotime($_GET['add_time_go']);
    			$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >= '{$addtimeg}' AND delivery_date <= '{$addtimed}' ORDER BY delivery_date DESC) AS T
    			GROUP BY T.order_id";
    			$db = &db();
    			$sql_c=$db->query($sql_count);
    			$mtm_time1 = array();
    			while($rows=@mysql_fetch_assoc($sql_c)){
    			$mtm_time1[]=$rows;
    			}
    			/* $mtm_time1 = $order_mtm_log->find(array(
    					'conditions'=>"delivery_date >='{$addtimeg1}' AND delivery_date <> 0",
    			)); */
    			$mtm_times=i_array_column($mtm_time1,'order_id');
    			$generaliys1=db_create_in($mtm_times,'order_figure.order_id');
    			$conditions.=" AND ".$generaliys1;
    		}elseif($_GET['add_time_do']){
    			$addtimed=strtotime($_GET['add_time_do']);
    			$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >= '{$addtimeg}' AND delivery_date <= '{$addtimed}' ORDER BY delivery_date DESC) AS T
    			GROUP BY T.order_id";
    			$db = &db();
    			$sql_c=$db->query($sql_count);
    			$mtm_time2 = array();
    			while($rows=@mysql_fetch_assoc($sql_c)){
    			$mtm_time2[]=$rows;
    			}
    			/* $mtm_time2 = $order_mtm_log->find(array(
    					'conditions'=>"delivery_date <='{$addtimed}' AND delivery_date <> 0",
    			)); */
    			$mtm_times2=i_array_column($mtm_time2,'order_id');
    			$generaliys2=db_create_in($mtm_times2,'order_figure.order_id');
    			$conditions.=" AND ".$generaliys2;
    		} 
    	}
    	
    	
    	if($_GET['field'] !='all' && $_GET['field'] && $_GET['search_name'])
    	{
    		if($_GET['field'] == 'glize')
    		{
    			$serve=m("serve");
    			$serves=$serve->find(array(
    					'conditions' =>"serve_name LIKE '%{$_GET['search_name']}%' ",
    			));
    			$generaliy=i_array_column($serves,'idserve');
    			$generaliys=db_create_in($generaliy,'order_figure.server_id');
    			$conditions.=" AND ".$generaliys;
    		}elseif($_GET['field'] == 'bd_name'){
    			$conditions.=" AND order_figure.liangti_name LIKE '%{$_GET['search_name']}%'";
    		}
    	}
    	
    	$serves=$this->_serve_mod->find(array(
    			'conditions' =>"1=1",
    			'fields' =>"idserve,userid,linkman,serve_name,card_number",
    			'index_key'=>"idserve",
    	));
    	if($serves){
    		foreach($serves as $sk=>$sv)
    		{
    			$servels[$sv['idserve']]=$sv['serve_name'];
    		}
    	}
    	$orders= $this->_orderfigure->find(array(
    			'conditions' =>"order_alias.extension= 'news' AND order_alias.status in (30,40)".$conditions,
    			'fields' =>"id,order_figure.liangti_id,order_figure.history_id,order_figure.son_sn,order_figure.server_id,order_figure.liangti_name,order_figure.order_id,order_figure.measure,order_figure.realname,order_alias.order_sn,order_alias.final_amount,order_alias.money_amount,order_alias.coin,order_alias.order_amount,order_alias.user_id,order_alias.add_time,order_alias.user_name,order_alias.status,order_alias.ship_time,order_alias.r_order_id",
    			'join'=>'has_order',
    			'order'=>"id DESC",
    	));
    	$status=array(
    			'11' => '待付款', //11 待付款
    			'12'=> '待量体', //12 待量体
    			'20' => '已支付', //20 已支付
    			'60'=> '生产中', //60 //生产中
    			'61'=> '备货中',   // 61
    			'30'=> '已发货', //30
    			'41' => '返修中', //41
    			'40' => '已完成', //40
    			'70' => '退款中',//70
    			'80' => '已退款',//70
    			'0'=> '已取消', //0
    			'43' => '订单异常', //43 订单异常(订单推送异常)
    			'44' => '物流异常', //44 订单异常(物流推送异常)
    	);
    	if($orders){
    		foreach($orders as $key=>$val)
    		{
    			if($val['liangti_id']){
    				$figure_liangti=m("figure_liangti");
    				$card_numbers=$figure_liangti->get(array(
    						'conditions' =>"liangti_id = '{$val['liangti_id']}'",
    						'join'=>'has_member',
    						'fields' =>"card_number,real_name",
    				));
    				$card_number=$card_numbers['card_number'];
    				if(!$val['liangti_name']){
    					$liangti_name=$card_numbers['real_name'];
    				}else{
    					$liangti_name=$val['liangti_name'];
    				}
    			}else{
    				if($val['server_id']){
    					$card_number=$serves[$val['server_id']]['card_number'];
    				}
    				if(!$val['liangti_name']){
    					$liangti_name=$serves[$val['server_id']]['linkman'];
    				}else{
    					$liangti_name=$val['liangti_name'];
    				}
    			}
    			$ordergoods=$this->_ordergoods->find(array(
    					'conditions' =>"order_id='{$val['order_id']}' group by goods_name having sum(quantity)",
    					'fields'=>"quantity,goods_name,sum(quantity) as squantity",
    			));
    			if($ordergoods){
    				$cloth="";
    				foreach($ordergoods as $ok=>$or){
    					$cloth .=$or['squantity']."件".$or['goods_name'];
    				}
    			}
    			if($val['measure'] ==5){
    				$customs=$customer_figure->get(array(
    						'conditions' =>"figure_sn='{$val['history_id']}'",
    						'fields'     =>"service_mode",
    				));
    				if($customs['service_mode'] == 3){
    					$orfigus= $this->_orderfigure->get(array(
    							'conditions' =>"id < '{$val['id']}' AND history_id = '{$val['history_id']}'",
    					));
    					if($orfigus){
    						$is_history='否';
    					}else{
    						$is_history='是';
    					}
    					$measure='线下采集';
    				}else{
    					if($customs['service_mode'] ==1){
    						$measure='上门';
    					}elseif($customs['service_mode'] ==2){
    						$measure='门店';
    					}elseif($customs['service_mode'] ==6){
    						$measure='指派量体师';
    					}elseif($customs['service_mode'] ==4){
    						$measure='后台录入';
    					}
    					$is_history='否';
    				}
    			}else{
    				$is_history='是';
    				if($val['measure'] ==1){
    					$measure='上门';
    				}elseif($val['measure'] ==2){
    					$measure='门店';
    				}elseif($val['measure'] ==6){
    					$measure='指派量体师';
    				}
    			}
    			if($val['son_sn']){
    				$figure_ors=$figure_orderm->get(array(
    						'conditions' =>"son_sn='{$val['son_sn']}'",
    						'fields' =>"modi_time",
    				));
    				if($figure_ors['modi_time']){
    					$modi_time=date("Y-m-d H:i:s",$figure_ors['modi_time']);
    				}else{
    					$fi_ors=$figure_orderm->get(array(
    							'conditions' =>"order_id='{$val['id']}'",
    							'fields' =>"modi_time",
    					));
    					if($fi_ors['modi_time']){
    						$modi_time=$fi_ors['modi_time'];
    					}else{
    						$modi_time='';
    					}
    						
    				}
    	
    			}else{
    				$figure_ors=$figure_orderm->get(array(
    						'conditions' =>"order_id='{$val['id']}'",
    						'fields' =>"modi_time",
    				));
    				if($figure_ors['modi_time']){
    					$modi_time=date("Y-m-d H:i:s",$figure_ors['modi_time']);
    				}else{
    					$modi_time='';
    				}
    			}
    			$money=$val['final_amount']+$val['money_amount']+$val['coin'];
    			if($val['order_amount']*0.3 ==$money){
    				$business="工装";
    			}/*elseif($val['order_amount']*0.3 > $money){
    				$business="测试";
    			}*/else{
    				$business=100;
    			}
    			$mtm_time = $order_mtm_log->get(array(
    					'conditions'=>"order_id ='{$val['order_id']}'",
    					'order'=>"delivery_date DESC",
    			));
    			
    			//$mtm_time = $order_mtm_log->get("order_id ='{$val['order_id']}'");
    			if($mtm_time){
    				$ship_time= date("Y-m-d H:i",$mtm_time['delivery_date']);
    			}else{
    			$ship_time='';
    		    }
    			$order[$val['id']] = array(
    					'serve_name'=>$servels[$val['server_id']],//门店
    					'liangti_name'=>$liangti_name,//量体师姓名
    					'card_number'=>$card_number,//量体证号
    					'user_name'=>$val['user_name'],//用户名
    					'add_time'=>date("Y-m-d H:i:s",$val['add_time']),//下单时间
    					'order_sn' =>"'".$val['order_sn'],//订单号
    					'r_order_id'=>$val['r_order_id'],//rcmtm订单号
    					'money'=>$money,//订单金额
    					'cloth'=>$cloth,//品类
    					'realname'=>$val['realname'],//被量体人
    					'measure'=>$measure,//量体数据来源
    					'modi_time'=>$modi_time,//量体数据录入时间
    					'is_history'=>$is_history,//是否第一次使用该量体数据
    					'status' =>$status[$val['status']],//订单状态
    					'ship_time'=>$ship_time,//发货时间
    					'is_fx'=>'否',//是否返修/退货
    					'business'=>$business,//量体费
    			);
    		}
    	}
    	
    	 
    	$fields_name = array('门店','量体师姓名','量体证号','用户名','下单时间','订单号','rcmtm订单号','订单金额','品类','被量体人姓名','量体数据来源','量体数据录入时间','是否第一次使用该量体数据','订单状态','发货时间','是否返修/退货','量体费');
    	array_unshift($order,$fields_name);
    	$this->export_to_csv($order, 'BD', 'gbk');
    	
    }
    
    
    function figu_export(){
    	$customer_figure=m("customer_figure");
    	$serves=$this->_serve_mod->find(array(
    			'conditions' =>"1=1",
    			'fields' =>"idserve,userid,linkman,serve_name",
    	));
    	if($serves){
    		foreach($serves as $sk=>$sv)
    		{
    			$servels[$sv['idserve']]=$sv['serve_name'];
    		}
    	}
    	$orders= $this->_orderfigure->find(array(
    			'conditions' =>"order_alias.extension= 'news' AND order_alias.status in (30,40)",
    			'fields' =>"id,order_figure.liangti_id,order_figure.history_id,order_figure.server_id,order_figure.liangti_name,order_figure.order_id,order_figure.measure,order_figure.realname,order_alias.order_sn,order_alias.final_amount,order_alias.money_amount,order_alias.coin,order_alias.order_amount,order_alias.user_id,order_alias.add_time,order_alias.user_name",
    			'limit' => $page['limit'],
    			'join'=>'has_order',
    			'order'=>"id DESC",
    	));
    	if($orders){
    		foreach($orders as $key=>$val)
    		{
    			
    			$ordergoods=$this->_ordergoods->find(array(
    					'conditions' =>"order_id='{$val['order_id']}' group by goods_name having sum(quantity)",
    					'fields'=>"quantity,goods_name,sum(quantity) as squantity",
    			));
    			if($ordergoods){
    				$cloth="";
    				foreach($ordergoods as $ok=>$or){
    					$cloth .=$or['squantity']."件".$or['goods_name'];
    				}
    			}
    			if($val['measure'] ==5){
    				$customs=$customer_figure->get(array(
    						'conditions' =>"figure_sn='{$val['history_id']}'",
    						'fields'     =>"service_mode",
    				));
    				if($customs['service_mode'] == 3){
    					$orfigus= $this->_orderfigure->get(array(
    							'conditions' =>"id < '{$val['id']}' AND history_id = '{$val['history_id']}'",
    					));
    					if($orfigus){
    						$is_history='否';
    					}else{
    						$is_history='是';
    					}
    					$measure='线下采集';
    				}else{
    					if($customs['service_mode'] ==1){
    						$measure='上门';
    					}elseif($customs['service_mode'] ==2){
    						$measure='门店';
    					}elseif($customs['service_mode'] ==6){
    						$measure='指派量体师';
    					}elseif($customs['service_mode'] ==4){
    						$measure='后台录入';
    					}
    					$is_history='否';
    				}
    			}else{
    				$is_history='是';
    				if($val['measure'] ==1){
    					$measure='上门';
    				}elseif($val['measure'] ==2){
    					$measure='门店';
    				}elseif($val['measure'] ==6){
    					$measure='指派量体师';
    				}
    			}
    			$money=$val['final_amount']+$val['money_amount']+$val['coin'];
    			if($val['order_amount']*0.3 ==$money){
    				$business="工装";
    			}elseif($val['order_amount']*0.3 > $money){
    				$business="测试";
    			}else{
    				$business=60;
    			}
    			 
    			$order[$val['id']] = array(
    					'order_sn' =>"'".$val['order_sn'],//订单号
    					'add_time'=>date("Y-m-d H:i:s",$val['add_time']),//下单时间
    					'user_name'=>$val['user_name'],//下单账号
    					'cloth'=>$cloth,//订单产品
    					'money'=>$money,//订单金额
    					'realname'=>$val['realname'],//被量体人
    					'measure'=>$measure,//量体数据来源
    					'liangti_name'=>$val['liangti_name'],//量体师姓名
    					'serve_name'=>$servels[$val['server_id']],//量体师归属
    					'business'=>$business,//量体费
    					'is_history'=>$is_history,//是否第一次使用该量体数据
    					'is_fx'=>'',//是否返修/退货
    					);
    			
    		}
    	}
    	
    }
    
    
    
   
}
?>