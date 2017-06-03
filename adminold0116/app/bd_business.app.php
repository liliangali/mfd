<?php
/* BD业务统计
 * 2015-12-14 by shao
 *  */
class Bd_businessApp  extends BackendApp
{
	var $_member_mod;
	var $_generalize_mod;
	var $_g_mod;
	var $_invite_mod;
	var $_order_mod;
	var $_member_lv;
	function __construct(){
		$this->_member_mod = &m("member");
		$this->_generalize_mod =& m('generalize_member');
		$this->_g_mod = & m('generalize');
		$this->_invite_mod = & m('memberinvite');
		$this->_order_mod = & m('order');
		 $this->_member_lv = & m('memberlv'); 
           parent::__construct();
    }


    //列表
    function index(){
    	$order_mtm_log = & m('ordermtmlogs');
    	 if($_GET['add_time_from']){
    		$addtimef=strtotime($_GET['add_time_from']);
    	   $conorder=" AND add_time >='{$addtimef}'";
    	}
    	if($_GET['add_time_to']){
    		$addtimet=strtotime($_GET['add_time_to']);
    		$conorder.=" AND add_time <='{$addtimet}'";
    	}
    	/* if($_GET['add_time_go']){
    		$addtimeg=strtotime($_GET['add_time_go']);
    		$conorder.=" AND ship_time >='{$addtimeg}'";
    	}
    	if($_GET['add_time_do']){
    		$addtimed=strtotime($_GET['add_time_do']);
    		$conorder.=" AND ship_time <='{$addtimed}'";
    	} */
    	
    	if($addtimef && $addtimet){
    		if($addtimef>$addtimet){
    			$this->show_message("搜索起始时间不能大于终止时间");
    			return false;
    		}
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
    		$generaliyy=db_create_in($mtm_timeys,'order_id');
    		$conorder.=" AND ".$generaliyy;
    	}else{
    		if($_GET['add_time_go']){
    			$addtimeg1=strtotime($_GET['add_time_go']);
    			$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >='{$addtimeg1}' AND delivery_date <> 0 ORDER BY delivery_date DESC) AS T
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
    			$generaliys1=db_create_in($mtm_times,'order_id');
    			$conorder.=" AND ".$generaliys1;
    		}elseif($_GET['add_time_do']){
    			$addtimed=strtotime($_GET['add_time_do']);
    			$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date <='{$addtimed}' AND delivery_date <> 0 ORDER BY delivery_date DESC) AS T
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
    			$generaliys2=db_create_in($mtm_times2,'order_id');
    			$conorder.=" AND ".$generaliys2;
    		}
    	}
    	
   
    	
    	
    	
    	
    	if($_GET['field'] !='all' && $_GET['field'] && $_GET['search_name'])
    	{
    		if($_GET['field'] == 'glize')
    		{
    			$conditi_glize=" AND name LIKE '%{$_GET['search_name']}%'";
    		}elseif($_GET['field'] == 'bd_name'){
    			$gen_eraliys=" AND name LIKE '%{$_GET['search_name']}%'";
    		}
    	}
    	$order_goods=m("ordergoods");
    	//generalizen所属组织表  （ 所属组织
    	//generalize_memberBD顾问表   （ BD码  BD姓名
    	//order订单表    （  用户id  下单时间	订单号	订单金额
    	//member_invite顾问关联会员表    （ 顾问
    	//member会员表    （ 用户名	用户级别
    	//order_goods表订单的品类    （  产品品类  个数
       //所属组织	BD码	BD姓名	用户名	用户级别	下单时间	订单号	订单金额	产品品类	提成	是否退款
       //member_lv 会员等级表
    	$generalizes=$this->_g_mod->find(array(
    			'conditions' =>'1=1'.$conditi_glize,
    			'index_key' =>'id',
    	));
    	
    	foreach($generalizes as $key=>$val)
    	{
    		$generalists[$key]=$val['name'];
    	}
    	$this->assign('generalists',$generalists);
    	
    		$generaliy=i_array_column($generalizes,'id');
    		$generaliys=db_create_in($generaliy,'g_id');
    		$generalize_s=$this->_generalize_mod->find(array(
    				'conditions'=>$generaliys.$gen_eraliys,
    				'fields' =>"id",
    		));
    		$generali_y=i_array_column($generalize_s,'id');
    		$conditions =" AND ".db_create_in($generali_y,'inviter');
    		
    	 $memberlvs=$this->_member_lv->find(array(
    			'conditions'=>'1=1',
    	));
    	foreach($memberlvs as $key=>$val){
    		$melvs[$val['member_lv_id']]=$val['name'];
    	}
    	$this->assign('melvs',$melvs); 
    	$page = $this->_get_page(30);
    	setcookie('curr_page',$page["curr_page"]);
    	$list = $this->_member_mod->find(array(
    			'join'          => 'has_member',
    			'conditions'    => "member_invite.type=1".$conditions,
    			'fields'        => 'member.user_id,member.nickname,member.user_name,member.member_lv_id,member_invite.inviter,member_invite.invitee,member_invite.id,member_invite.add_time',
    			'order'         => "member_invite.add_time DESC",
    	));
    	$list_array=i_array_column($list,'user_id');
    	
    	$user_id=db_create_in($list_array,'user_id');
    	$ordercos=$this->_order_mod->find(array(
    			'conditions' =>$user_id."AND extension= 'news' AND status in (30,40)".$conorder,
    			'fields' =>'order_id',
    	));
    	
    	$orderlists=$this->_order_mod->find(array(
    			'conditions' =>$user_id."AND extension= 'news' AND status in (30,40)".$conorder,
    			'limit' => $page['limit'],
    			'fields' =>'order_id,order_sn,final_amount,money_amount,coin,order_amount,user_id,add_time,ship_time,status,express,r_order_id,payment_name',
    			'order'  =>"add_time DESC",
    			
    	));
    	foreach($orderlists as $key=>$val){
    		$glize=$this->_generalize_mod->get(array(
    				conditions=>"id='{$list[$val['user_id']]['inviter']}'",
    		));
    		$ordergoods=$order_goods->find(array(
    				'conditions' =>"order_id='{$val['order_id']}' group by goods_name having sum(quantity)",
    				'fields'=>"quantity,goods_name,sum(quantity) as squantity",
    		));
    		if($ordergoods){
    			$cloth="";
    			foreach($ordergoods as $ok=>$or){
    				$cloth .=$or['squantity']."件".$or['goods_name'];
    			}
    		}
    		$money=$val['final_amount']+$val['money_amount']+$val['coin'];
    		$money_ser=$val['final_amount']+$val['money_amount'];
    		$paytype='';
    		if($val['final_amount']>0){
    			$paytype.="'{$val['payment_name']}']支付：￥'{$val['final_amount']}'</br>";
    		}
    		if($val['money_amount']>0){
    			$paytype.="余额支付：￥'{$val['money_amount']}'</br>";
    		}
    		if($val['coin']>0){
    			$paytype.="麦富迪币支付：￥'{$val['coin']}'";
    		}
    		if($val['order_amount']*0.3 ==$money){
    			$business="0（工装）";
    		}/* elseif($val['order_amount']*0.3 > $money){
    			$business="0（测试）";
    		} */else{
    			/*  if($list[$val['user_id']]['member_lv_id'] == 1)
    			{
    				$business=$money_ser*0.05;
    			}elseif($list[$val['user_id']]['member_lv_id'] == 2)
    			{
    				$business=$money_ser*0.15;
    			}elseif($list[$val['user_id']]['member_lv_id'] == 3){
    				$business=$money_ser*0.1;
    			}elseif($list[$val['user_id']]['member_lv_id'] == 4){
    				$business=$money_ser*0.05;
    			}else{
    				$business=0;
    			}  */
    		if($list[$val['user_id']]['member_lv_id'] == 8){
    			$business=0;
    		}else{
    		    $business=$money_ser*0.1;
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
    				if($val['ship_time']){
    					$ship_time= date("Y-m-d H:i",$val['ship_time']);
    				}else{
    					$ship_time='';
    				}
    		    }
    		    
    		$mtm_ids =array_filter(explode(',', $val['r_order_id']));
    		$orderlists[$key]['glize']=$glize['g_id'];
    		$orderlists[$key]['invite']=$glize['invite'];
    		$orderlists[$key]['bd_name']=$glize['name'];
    		$orderlists[$key]['cloth']=$cloth;
    		$orderlists[$key]['add_time']=date("Y-m-d H:i:s",$val['add_time']?$val['add_time']:0);
    		$orderlists[$key]['nickname']=$list[$val['user_id']]['nickname'];
    		$orderlists[$key]['member_lv']=$list[$val['user_id']]['member_lv_id'];
    		$orderlists[$key]['inviter']=$list[$val['user_id']]['inviter'];
    		$orderlists[$key]['money']=$money;
    		$orderlists[$key]['business']=$business;
    		$orderlists[$key]['paytype']=$paytype;
    		$orderlists[$key]['ship_times']=$ship_time;
    		$orderlists[$key]['r_order_id']=$mtm_ids;
    		//$orderlists[$key]['ship_time']=date("Y-m-d H:i:s",$val['ship_time']);
    	}
    	$page['item_count'] = count($ordercos);
    	$this->_format_page($page);
    	$this->assign('page_info', $page);
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
    	$this->assign('orderlist',$orderlists);
    	$this->assign('lists',$list);
    	$this->assign('selects',array(
    			//'all'=>'全部',
                'glize'   => '组织名',
                'bd_name'   => '顾问姓名',
    	));
    	$this->assign('query',$_GET);
        $this->display('bd_business/bd_business.index.html');
    }
    
    function export(){
    	$order_mtm_log = & m('ordermtmlogs');
    	 if($_GET['add_time_from']){
    	 $addtimef=strtotime($_GET['add_time_from']);
    	$conorder=" AND add_time >='{$addtimef}'";
    	}
    	if($_GET['add_time_to']){
    	$addtimet=strtotime($_GET['add_time_to']);
    	$conorder.=" AND add_time <='{$addtimet}'";
    	}
    	/* if($_GET['add_time_go']){
    	$addtimeg=strtotime($_GET['add_time_go']);
    	$conorder.=" AND ship_time >='{$addtimeg}'";
    	}
    	if($_GET['add_time_do']){
    	$addtimed=strtotime($_GET['add_time_do']);
    	$conorder.=" AND ship_time <='{$addtimed}'";
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
    		
    		$mtm_timeys=i_array_column($mtm_timey,'order_id');
    		$generaliyy=db_create_in($mtm_timeys,'order_id');
    				$conorder.=" AND ".$generaliyy;
    	}else{
    	if($_GET['add_time_go']){
    		$addtimeg1=strtotime($_GET['add_time_go']);
    			$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >='{$addtimeg1}' AND delivery_date <> 0 ORDER BY delivery_date DESC) AS T
    			GROUP BY T.order_id";
    			$db = &db();
    			$sql_c=$db->query($sql_count);
    			$mtm_time1 = array();
    			while($rows=@mysql_fetch_assoc($sql_c)){
    			$mtm_time1[]=$rows;
    			}
    			
    			$mtm_times=i_array_column($mtm_time1,'order_id');
    			 $generaliys1=db_create_in($mtm_times,'order_id');
    			$conorder.=" AND ".$generaliys1;
    			}elseif($_GET['add_time_do']){
    			$addtimed=strtotime($_GET['add_time_do']);
    			$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date <='{$addtimed}' AND delivery_date <> 0 ORDER BY delivery_date DESC) AS T
    			GROUP BY T.order_id";
    			$db = &db();
    			$sql_c=$db->query($sql_count);
    			$mtm_time2 = array();
    			while($rows=@mysql_fetch_assoc($sql_c)){
    			$mtm_time2[]=$rows;
    			}
    		
    			$mtm_times2=i_array_column($mtm_time2,'order_id');
    			$generaliys2=db_create_in($mtm_times2,'order_id');
    			$conorder.=" AND ".$generaliys2;
    			}
    			}
    			 
    	
    	
    	if($_GET['field'] !='all' && $_GET['field'])
    	{
    	if($_GET['field'] == 'glize')
    	{
    	$conditi_glize=" AND name LIKE '%{$_GET['search_name']}%'";
    	}elseif($_GET['field'] == 'bd_name'){
    	$gen_eraliys=" AND name LIKE '%{$_GET['search_name']}%'";
    	}
    	}
    	$order_goods=m("ordergoods");
    	$generalizes=$this->_g_mod->find(array(
    			'conditions' =>'1=1'.$conditi_glize,
    			'index_key' =>'id',
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
    	foreach($generalizes as $key=>$val)
    	{
    	$generalists[$key]=$val['name'];
    	}
    	$generaliy=i_array_column($generalizes,'id');
    	$generaliys=db_create_in($generaliy,'g_id');
    	$generalize_s=$this->_generalize_mod->find(array(
    			'conditions'=>$generaliys.$gen_eraliys,
    			'fields' =>"id",
    	));
    	$generali_y=i_array_column($generalize_s,'id');
    	$conditions =" AND ".db_create_in($generali_y,'inviter');
    	 
    	$memberlvs=$this->_member_lv->find(array(
    			'conditions'=>'1=1',
    	));
    	foreach($memberlvs as $key=>$val){
    	$melvs[$val['member_lv_id']]=$val['name'];
    	}
    	$list = $this->_member_mod->find(array(
    			'join'          => 'has_member',
    			'conditions'    => "member_invite.type=1".$conditions,
    			'fields'        => 'member.user_id,member.nickname,member.user_name,member.member_lv_id,member_invite.inviter,member_invite.invitee,member_invite.id,member_invite.add_time',
    			'order'         => "member_invite.add_time DESC",
    	));
    	$list_array=i_array_column($list,'user_id');
    
    	$user_id=db_create_in($list_array,'user_id');
    	
    	$orderlists=$this->_order_mod->find(array(
    			'conditions' =>$user_id."AND extension= 'news' AND status in (30,40)".$conorder,
    			'fields' =>'order_id,order_sn,final_amount,money_amount,coin,order_amount,user_id,add_time,ship_time,status,express,r_order_id,payment_name',
    			'order'  =>"add_time DESC",
    
    	));
    	
    	
    	
    	if($orderlists){
    		foreach($orderlists as $key=>$val){
    			$glize=$this->_generalize_mod->get(array(
    					conditions=>"id='{$list[$val['user_id']]['inviter']}'",
    			));
    			$ordergoods=$order_goods->find(array(
    					'conditions' =>"order_id='{$val['order_id']}' group by goods_name having sum(quantity)",
    					'fields'=>"quantity,goods_name,sum(quantity) as squantity",
    			));
    			if($ordergoods){
    				$cloth="";
    				foreach($ordergoods as $ok=>$or){
    					$cloth .=$or['squantity']."件".$or['goods_name'];
    				}
    			}
    			$money=$val['final_amount']+$val['money_amount']+$val['coin'];
    			$money_ser=$val['final_amount']+$val['money_amount'];
    			$paytype='';
    			if($val['final_amount']>0){
    				$paytype.="'{$val['payment_name']}']支付：￥'{$val['final_amount']}'|";
    			}
    			if($val['money_amount']>0){
    				$paytype.="余额支付：￥'{$val['money_amount']}'|";
    			}
    			if($val['coin']>0){
    				$paytype.="麦富迪币支付：￥'{$val['coin']}'";
    			}
    			if($val['order_amount']*0.3 ==$money){
    				$business="0（工装）";
    			}/* elseif($val['order_amount']*0.3 > $money){
    			$business="0（测试）";
    			} */else{
    			/*  if($list[$val['user_id']]['member_lv_id'] == 1)
    			 {
    			$business=$money_ser*0.05;
    			}elseif($list[$val['user_id']]['member_lv_id'] == 2)
    			{
    			$business=$money_ser*0.15;
    			}elseif($list[$val['user_id']]['member_lv_id'] == 3){
    			$business=$money_ser*0.1;
    			}elseif($list[$val['user_id']]['member_lv_id'] == 4){
    			$business=$money_ser*0.05;
    			}else{
    			$business=0;
    			}  */
    			if($list[$val['user_id']]['member_lv_id'] == 8){
    				$business=0;
    			}else{
    				$business=$money_ser*0.1;
    			}
    			}
    			if($val['add_time']){
    				$add_time=date("Y-m-d H:i:s",$val['add_time']);
    			}else{
    				$add_time='';
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
    		
    			$order[$val['order_id']] = array(
    					'glize'=>$generalists[$glize['g_id']],//组织名称
    					'bd_name'=>$glize['name'],//顾问姓名
    					'invite'=>$glize['invite'],//BD码
    					'nickname'=>$list[$val['user_id']]['nickname'],//用户名
    					'member_lv'=>$melvs[$list[$val['user_id']]['member_lv_id']],//用户级别
    					'add_time'=>date("Y-m-d H:i:s",$val['add_time']),//下单时间
    					'order_sn'=>"'".$val['order_sn'],//订单号
    					'r_order_id'=>$val['r_order_id'],//rcmtm订单号
    					'money'=>$money,//订单金额
    					'paytype'=>$paytype,//支付方式
    					'cloth'=>$cloth,//品类
    					'status'=>$status[$val['status']],//订单状态
    					'ship_times'=>$ship_time,//发货时间
    					'express'=>$val['express'],//运单号/物流单号
    					'is_tk'=>'否',
    					'business'=>$business,//提成
    			);
    		}
    		$fields_name = array('组织名称','顾问姓名','BD码','用户名','用户级别','下单时间','订单号','rcmtm订单号','订单金额','支付方式','品类','订单状态','发货时间','运单号/物流单号','是否退款','提成金额');
    		array_unshift($order,$fields_name);
    		$this->export_to_csv($order, 'BD', 'gbk');
    	}
    }
    
    
    
   function figu_export() {
  	
  	if($_GET['add_time_from']){
  		$addtimef=gmstr2time($_GET['add_time_from']);
  		$conorder=" AND add_time >='{$addtimef}'";
  	}elseif($_GET['add_time_to']){
  		$addtimet=gmstr2time($_GET['add_time_to']);
  		$conorder.=" AND add_time <='{$addtimet}'";
  	}elseif($_GET['add_time_go']){
  		$addtimeg=gmstr2time($_GET['add_time_go']);
  		$conorder.=" AND ship_time >='{$addtimeg}'";
  	}elseif($_GET['add_time_do']){
  		$addtimed=gmstr2time($_GET['add_time_do']);
  		$conorder.=" AND ship_time <='{$addtimed}'";
  	}
  	if($_GET['field'] !='all' && $_GET['field'])
  	{
  		if($_GET['field'] == 'glize')
  		{
  			$conditi_glize=" AND name LIKE '%{$_GET['search_name']}%'";
  		}elseif($_GET['field'] == 'bd_name'){
  			$gen_eraliys=" AND name LIKE '%{$_GET['search_name']}%'";
  		}
  	}
  	
  	
  	$order_goods=m("ordergoods");
  	$generalizes=$this->_g_mod->find(array(
  			'conditions' =>'1=1'.$conditi_glize,
  			'index_key' =>'id',
  	));
  	foreach($generalizes as $key=>$val)
  	{
  		$generalists[$key]=$val['name'];
  	}
  	$generaliy=i_array_column($generalizes,'id');
  	$generaliys=db_create_in($generaliy,'g_id');
  	$generalize_s=$this->_generalize_mod->find(array(
  			'conditions'=>$generaliys.$gen_eraliys,
  			'fields' =>"id",
  	));
  	$generali_y=i_array_column($generalize_s,'id');
  	$conditions =" AND ".db_create_in($generali_y,'inviter');
  	$memberlvs=$this->_member_lv->find(array(
  			'conditions'=>'1=1',
  	));
  	foreach($memberlvs as $key=>$val){
  		$melvs[$val['member_lv_id']]=$val['name'];
  	}
  	$list = $this->_member_mod->find(array(
  			'join'          => 'has_member',//
  			'conditions'    =>' member_invite.type=1'.$conditions,
  			'fields'        => 'member.user_id,member.nickname,member.user_name,member.member_lv_id,member_invite.inviter,member_invite.invitee,member_invite.id,member_invite.add_time',
  			'order'         => "member_invite.add_time DESC",
  	));
  	$list_array=i_array_column($list,'user_id');
  	$user_id=db_create_in($list_array,'user_id');
  	$orderlists=$this->_order_mod->find(array(
  			'conditions' =>$user_id."AND extension= 'news' AND status in (30,40)",
  			'fields' =>'order_id,order_sn,final_amount,money_amount,coin,order_amount,user_id,add_time',
  			'order'  =>"add_time DESC",
  			 
  	));
  
  	foreach($orderlists as $key=>$val){
  		$glize=$this->_generalize_mod->get(array(
  				conditions=>"id='{$list[$val['user_id']]['inviter']}'",
  		));
  		$ordergoods=$order_goods->find(array(
  				'conditions' =>"order_id='{$val['order_id']}' group by goods_name having sum(quantity)",
  				'fields'=>"quantity,goods_name,sum(quantity) as squantity",
  		));
  		if($ordergoods){
  			$cloth="";
  			foreach($ordergoods as $ok=>$or){
  				$cloth .=$or['squantity']."件".$or['goods_name'];
  			}
  		}
  		$money=$val['final_amount']+$val['money_amount']+$val['coin'];
  		if($val['order_amount']*0.3 ==$money){
  			$business="工装";
  		}elseif($val['order_amount']*0.3 > $money){
  			$business="测试";
  		}else{
  			if($list[$val['user_id']]['member_lv_id'] == 1)
  			{
  				$business=$money*0.05;
  			}elseif($list[$val['user_id']]['member_lv_id'] == 2)
  			{
  				$business=$money*0.15;
  			}elseif($list[$val['user_id']]['member_lv_id'] == 3){
  				$business=$money*0.1;
  			}elseif($list[$val['user_id']]['member_lv_id'] == 4){
  				$business=$money*0.05;
  			}else{
  				$business=0;
  			}
  		}
  		$order[$val['order_id']] = array(
  		'glize'=>$generalists[$glize['g_id']],//所属组织
  		'invite'=>$glize['invite'],//BD码
  		'bd_name'=>$glize['name'],//BD姓名
  		'nickname'=>$list[$val['user_id']]['nickname'],//用户名
  		'member_lv'=>$melvs[$list[$val['user_id']]['member_lv_id']],//用户级别
  		'add_time'=>date("Y-m-d H:i:s",$val['add_time']),//下单时间
  		'order_sn'=>"'".$val['order_sn'],//订单号
  		'money'=>$money,//订单金额
  		'cloth'=>$cloth,//产品品类
  		'business'=>$business,//提成
  		'is_tk'=>'',
  		     );
  	}
  	$fields_name = array('所属组织','BD码','BD姓名','用户名','用户级别','下单时间','订单号','订单金额','产品品类','提成','是否退款');
  	array_unshift($order,$fields_name);
  	$this->export_to_csv($order, 'BD', 'gbk');
  }  
     
    
   
}
?>