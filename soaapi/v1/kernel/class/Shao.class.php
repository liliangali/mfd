<?php
	
	/**
	 * 
	 * @author shaozizhen
	 * @version v1
	 * @copyright Copyright 2014 figure.com
	 * @package app
	 */
////http://local.soaapi.cotte.com/soap/shao.php?act=order_assess&token=eda6b5ebb68702e0e49841cb9aeb50d3
	class Shao extends Result
	{
		//重要资讯
		//http://local.soaapi.cotte.com/soap/shao.php?act=informations&trade=1
		function informations($data)
		{
			global $json;
			$trade     = isset($data->trade) ? $data->trade: '';
			$informations=m("informations");
    	    $infor_gallery=m("infor_gallery");
    	    if (!$trade)
    	    {
    	    	$this->errorCode = 100;
    	    	$this->msg = '未传平台类型';//1ios2安卓
    	    	return $this->eresult();
    	    }
    	    if(!in_array($trade,array(1,2)))
    	    {
    	    	$this->errorCode = 101;
    	    	$this->msg = '所传平台参数错误';//1ios2安卓
    	    	return $this->eresult();
    	    }
    	    if($trade == 1){
    	    	$conditions=" AND trade in (0,1)";
    	    }else{
    	    	$conditions=" AND trade in (0,2)";
    	    }	
    	    $informations=$informations->get(array(
    	    		'conditions' =>"state=1".$conditions,
    	    ));
			if($informations['con_type'] ==2)
			{
				$gallerys=$infor_gallery->find(array(
						'conditions' =>"infor_id='{$informations['id']}'",
						'fields' =>'l_img',
						 
				));
				foreach($gallerys as $key=>$val){
					$gallery[]=$val['l_img'];
				}
				$informations['content']=$gallery;
			}else{
				$informations['content']=array($informations['content']);
			}
			$return = array(
					'statusCode' => 1,
					'result'     => array(
							'infor'   => $informations,//待量体数
					),
			);
			return $json->encode($return);
			
		}
		//http://local.api.mfd.com/soap/shao.php?act=activation&token=a8aa63ff5fd804cf6057c5ca8cd9487a&actnum=HEUAGVWVHE
		//激活券
		function activation($data)
		{
			global $json;
			$debit_mod=&m("voucher");
			$batch_mod=&m("voucher_batch");
			$actnum     = isset($data->actnum) ? $data->actnum: '';//激活码
			$token     = isset($data->token) ? $data->token: '';//用户标识
			$user_info = getUserInfo($token);
			if (!$user_info)
			{
				$this->errorCode = 100;
				$this->msg = '账号不存在！';
				return $this->eresult();
			}
			
			if (!$actnum )
			{
				$this->errorCode = 103;
				$this->msg = '激活码不存在或已失效！';
				return $this->eresult();
			}
			
			if(strlen($actnum) != 10){
				$this->errorCode = 104;
				$this->msg = '激活码格式错误！';
				return $this->eresult();
			}
			$time=time();
			$debit=$debit_mod->get(array(
		    'conditions' =>"code='{$actnum}' AND use_status=0 AND start_time < '{$time}'  AND source= 1  AND  '{$time}' < end_time  AND binding_user_id=0",	
			));
	
			if(!$debit){
				$this->errorCode = 101;
				$this->msg = '券不存在或已失效！';
				return $this->eresult();
			}
			if($debit['batch_id']){
			    $debit1=$batch_mod->get(array(
			        'conditions' =>" status=1 AND id='{$debit['batch_id']}'",
			    ));
			}
			
			if(!$debit1){
			   
			    $this->errorCode = 110;
			    $this->msg = '券不存在或已失效！';
			    return $this->eresult();
			}	
			$datt=array(
                  'binding_user_id'=>$user_info['user_id'],	
				  'binding_time'=>time(),
				  'binding_username'=>$user_info['user_name'],
				  'source'=>1,
			);
			
			$debit_mod->edit("code='{$actnum}'",$datt);
			$return = array(
					'statusCode' => 1,
			        'result'      => array(
			        'success'      => '激活成功',
			    ),
			);
			return $json->encode($return);
		}
	//http://local.api.mfd.com/soap/shao.php?act=coupon&token=a8aa63ff5fd804cf6057c5ca8cd9487a&type=1
	//我的优惠券
		function coupon($data)
		{
			global $json;
			$debit_mod=&m("voucher");
			$type     = isset($data->type) ? $data->type: '1';//类型（1未使用2已使用3已过期）
			$token     = isset($data->token) ? $data->token: '';//用户标识
			$user_info = getUserInfo($token);
			if (!$user_info)
			{
				$this->errorCode = 100;
				$this->msg = '账号不存在！';
				return $this->eresult();
			}
		
			$time=time();
			if(!in_array($type, array('1','2','3'))){
				$this->errorCode = 101;
				$this->msg = '券类型传参错误！';
				return $this->eresult();
			}
			if($type==1){
				$content=" AND use_status=0 AND '{$time}' < end_time";
			}elseif($type==2){
				$content=" AND use_status=1 ";
			}else{
				$content=" AND  '{$time}' > end_time AND use_status=0 ";
			}
			
			$debits=$debit_mod->find(array(
		    'conditions' =>" binding_user_id= ".$user_info['user_id'].$content,	
			));
			//使用数
		  $num_list = $this->debitNum($user_info);
		  
	     //判断是否过期
          if($debits){
	         foreach($debits as $key=>$val){
				 //code null转''
				 if(!$val['code']){
					$debits[$key]['code']=''; 
				 }
	             //剩余时间
	             $datetime=$val['end_time']-$time;
	             $datetimes=ceil($datetime/3600/24);
			
		         $debits[$key]['datetime']=$datetimes;
		         //来源
		       
		             $debits[$key]['source']="";
		       
		          if($time > $val['end_time']){
					  $debits[$key]['is_end']=0;
				  }else{
					  $debits[$key]['is_end']=1;
				  }
	            }
               }
			   $debit=array_values($debits);
			   
			$return = array(
					'statusCode' => 1,
			        'result'     => array(
			            'coupon'=>$debit,
					    'notUse'=>$num_list['notUse'],
					    'haveUsed'=>$num_list['haveUsed'],
					    'haveInvalid'=>$num_list['haveInvalid'],
			          ),
					
			);
			return $json->encode($return);
		}
	 function debitNum($user)
    {
        $user_id = $user['user_id'];
        $debit_mod = m('voucher');
        $times=time();
        $conditions1 = "binding_user_id = $user_id  AND use_status = 0 AND end_time > $times";
        $conditions2 = "binding_user_id = $user_id  AND use_status = 1";
        $conditions3 = "binding_user_id = $user_id  AND end_time < $times";
        
        $notUse = $debit_mod->get(array(
            'conditions' => $conditions1,
            'fields' => "count(*) as num",
        ));
         
        $haveUsed = $debit_mod->get(array(
            'conditions' => $conditions2,
            'fields' => "count(*) as num",
        ));
         
        $haveInvalid = $debit_mod->get(array(
            'conditions' => $conditions3,
            'fields' => "count(*) as num",
        ));
//  echo $conditions3;exit;        
        $list['notUse'] = $notUse['num'];
        $list['haveUsed'] = $haveUsed['num'];
        $list['haveInvalid'] = $haveInvalid['num'];
// print_exit($list);
        return $list;
    }
	
		//订单评价列表
		function order_assess($data)
		{
			global $json;
			$order_mod=m("order");
			$order_suit=m("ordersuit");
			$token     = isset($data->token) ? $data->token: '';
			$user_info = getUserInfo($token);
			if (!$user_info)
			{
				$this->errorCode = 100;
				$this->msg = '账号不存在';
				return $this->eresult();
			}
			
			$order_num=$order_mod->find(array(
					'conditions' =>"user_id={$user_info['user_id']} AND status=40 AND extension = 'news'",
					//'fields'    =>"order_id",
			));
			foreach($order_num as $key=>$val){
				/* $suity=$order_suit->get(array(
						'conditions' =>"order_id='{$val['order_id']}'",
						'fields' =>"id",
				)); */
				$suits=$order_suit->get(array(
						'conditions' =>"order_id='{$val['order_id']}' AND comment=0",
						//'fields'=>"",
				));
				
				if(!$suits){
					$set_in[]=$key;
				}
			}
			
			if($set_in){
				
				foreach($set_in as $k=>$v){
					
					unset($order_num[$v]);
				}
				
			}
			
			//return $json->encode($order_num);
			$return = array(
					'statusCode' => 1,
					'result'     => array(
							'orderlist'   => $order_num,//待量体数
					),
			);
			return $json->encode($return);
		}
		//查已激活的酷卡
		//http://local.soaapi.cotte.com/soap/shao.php?act=kuka_cotte
		function kuka_cotte($data)
		{
			global $json;
			$special_code=m("special_code");
			$special=$special_code->find(array(
					'conditions' =>"is_used=1",
			));
			$return = array(
					'statusCode' => 1,
					'result' => array(
							'special' =>$special,
					),
			);
			return $json->encode($return);
		}
		//http://local.soaapi.cotte.com/soap/shao.php?act=order_num&token=e0f23b0cfaf8eb2a5ad59683330f773b
		//订单状态数统计
		function order_num($data)
		{
			global $json;
			$token     = isset($data->token) ? $data->token: '';
			$user_info = getUserInfo($token);
			if (!$user_info)
			{
				$this->errorCode = 100;
				$this->msg = '账号不存在';
				return $this->eresult();
			}
			//11待量体，20已付款，30待收货，待评价（已完成40的订单查order_suit表），返修退换（按单品来）select *　ｆｒｏｍ　ｃｆ＿ｏｒｄｅｒ＿ｓｅｒｖｅ　ｗｈｅｒｅ　ｕｓｅｒ＿ｉｄ＝ｘｘｘ　ａｎｄ　ｔｙｐｅ＝１
			$order_mod=m("order");
			$order_serve=m("orderserve");
			$order_suit=m("ordersuit");
			$detail_mod=m('detail_comment');
			$order_goods_mod=m("ordergoods");
			$order_modss1=$order_mod->get(array(
					'conditions' =>"user_id={$user_info['user_id']} AND extension = 'news' AND status =11 ",
					'fields'    =>"count(order_id) as count",
			));
			$order_modss2=$order_mod->get(array(
					'conditions' =>"user_id={$user_info['user_id']} AND extension = 'news' AND status =20 ",
					'fields'    =>"count(order_id) as count",
			));
			$order_modss3=$order_mod->get(array(
					'conditions' =>"user_id={$user_info['user_id']} AND extension = 'news' AND status =30 ",
					'fields'    =>"count(order_id) as count",
			));
			$order_num=$order_mod->find(array(
					'conditions' =>"user_id={$user_info['user_id']} AND status=40 ",
					'fields'    =>"order_id",
			));
			
			
			if($order_num){
				$order_nums=i_array_column($order_num,'order_id');
				$nums=db_create_in($order_nums,'order_id');
			}
		
			if(isset($nums)){
				
				//=====  获取评论状态   =====
				/*$order_suit_comment = $order_suit->find(array(
						"conditions" => "comment = 0 AND ".$nums." group by order_id,goods_id,dis_ident",
						//"index_key"  => "order_id",
						'fields'=>"id",
				));
				$order_goods_comment = $order_goods_mod->get(array(
						"conditions" => "comment = 0 AND type= 'diy' AND goods_id = 0 AND ".$nums,
						//'index_key'  => "order_id",
						'fields'=>"count(rec_id) as count",
				));*/
			    $order_goodss = $order_goods_mod->get(array(
						"conditions" => "comment = 0  AND ".$nums,
						//'index_key'  => "order_id",
						'fields'=>"count(rec_id) as count",
				));
			
				$suits['count']=$order_goodss['count'];
				/* $suits=$order_suit->get(array(
						'conditions' =>$nums." AND comment=0",
						'fields'=>"count(id) as count",
				)); */
			}else{
				$suits['count']='0';
			}
			
			$serves=$order_serve->get(array(
					'conditions' =>"user_id='{$user_info['user_id']}' AND type=1 AND status>0",
					'fields'=>"count(id) as count",
			));
		
			
			$return = array(
					'statusCode' => 1,
					'result'     => array(
							'num1'   => $order_modss1['count'] ? $order_modss1['count']: '0',//待付款
							'num2'   => $order_modss3['count'] ? $order_modss3['count']: '0',//待收货数
							'num3'   => $suits['count'] ? $suits['count'] : '0',//评价数
							'num4'    => $order_modss2['count'] ? $order_modss2['count']: '0',//待发货数
					),
			);
			return $json->encode($return);
			
		}
		/**
		 * 我的顾客列表
		 */
		////http://local.soaapi.cotte.com/soap/shao.php?act=cus_list&token=eda6b5ebb68702e0e49841cb9aeb50d3
		 function cus_list($data)
		{
			global $json;
			$token     = isset($data->token) ? $data->token: '';
			$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
			$pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
			$user_info = getUserInfo($token);
			
			if (!$user_info)
			{
				$this->errorCode = 100;
               $this->msg = '账号不存在';
               return $this->eresult();
			}
			$store_id = $user_info['user_id'];
			if ($user_info['serve_type'] != 1)
			{
				$this->errorCode = 101;
				$this->msg = '必须当前创业者才能查看自己的顾客';
				return $this->eresult();
			}
		
			$limit = ($pageSize * ($pageIndex-1)) . ','. $pageSize;
			$conditions = "storeid=".$store_id;
			$member_figure_mod = m('customer_figure');
			
			$figure_list =$member_figure_mod->find(array(
					'fields'			=> "figure_sn,storeid,customer_name,customer_mobile,order_num,is_apply,figure_state",
					'conditions' 		=> $conditions,
					'limit'				=> $limit,
					'order'			    => 'lasttime DESC',
					'index_key'			=> '',
			));
			$figure_list = $this->assoc_unique($figure_list, 'customer_mobile');
			$this->result = $figure_list;
			return $this->sresult();
		}
		
		function assoc_unique($arr, $key)
		{ 
		 	$tmp_arr = array(); 
		 	foreach($arr as $k => $v)
		 	{ 
		  		if(in_array($v[$key], $tmp_arr))
		  		{ 
		  		 unset($arr[$k]); 
		  		}else
		  		{ 
		   		$tmp_arr[] = $v[$key]; 
		  		} 
		 	} 
			rsort($arr);
			
		 	return $arr; 
		}
		//查看已有量体数据的顾客信息
		//http://api.test.cotte.cn/soap/shao.php?act=check&token=6c40da327b2c7633a945665b78cc34a4&figure_sn=1215
		//http://local.soaapi.cotte.com/soap/shao.php?act=check&token=eda6b5ebb68702e0e49841cb9aeb50d3&figure_sn=1306
		public function check($data)
		{
			global $json;
			$db = db();
		    $figure_sn   = isset($data->figure_sn) ? $data->figure_sn: '';
			$token     = isset($data->token) ? $data->token: '';
			$user_info = getUserInfo($token);
			$member_figure_mod = m('customer_figure');
			$order_mod        = m('order');
			$member_mod=m("member");
			$serve_mod = m('serve');
			if (!$user_info)
			{
				$this->errorCode = 100;
				$this->msg = '账号不存在';
				return $this->eresult();
			}
			if ($user_info['serve_type'] != 1)
			{
				$this->errorCode = 101;
				$this->msg = '必须当前创业者才能查看顾客';
				return $this->eresult();
			}
		  if(!$figure_sn){
			$this->errorCode = 102;
			$this->msg = '没有要查看的客户figure_sn';
			return $this->eresult();
		  }
		  $figure=$member_figure_mod->get(array(
		  		'conditions'  => "figure_sn='{$figure_sn}' AND figure_state=1",
		  ));
		  if(!$figure) {
			  
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 103,
					'msg'       => '未发现对应量体数据！'
				),
			);
		    return $json->encode($return);
		  }
			$user_id = $userInfo['user_id'];
			//顾客基本信息
			
			$all_order_num = $order_mod->get(array(
				'conditions' => "user_name = {$figure['customer_mobile']} and status != 0 and extension = 'news' ",
				'fields'	 => "count(order_id) as count",
			));	
			$serve_mode= array(  
					'1' => '上门量体', //=====    =====
					'2' => '到店量体', //=====    =====
					'3' => '线下采集',//=====    =====
					'4' => '后台录入',//=====    =====
					'6' => '指派量体师',//=====    =====
			);
			if($figure['id_serve']){
				$serve_info = $serve_mod->get_info($figure['id_serve']);
			}
			
			if($figure['liangti_id'])
			{
				$liangti_id = $figure['liangti_id'];
				$liangti_info = $member_mod->get_info($liangti_id);
				if ($liangti_info)
				{
					$real_name = $figure['liangti_name'];
					$phone= $liangti_info['user_name'];
				}
			}
			else
			{
				if ($serve_info)
				{
					$real_name = $serve_info['linkman'];
					$phone= $serve_info['mobile'];
					
				}
			}
			if($serve_info){
				$serve_name = $serve_info['serve_name'];
				$serve_address = $serve_info['serve_address'];
				$store_mobile = $serve_info['store_mobile'];
			}
			
			$worker = array(
					'customer_name' => $figure['customer_name'],
					'customer_mobile'   => $figure['customer_mobile'],
					'order_num'   => !empty($all_order_num['count']) ? $all_order_num['count'] : 0,
					'lasttime'   => date('Y-m-d',$figure['lasttime']),
					'firsttime'   => $figure['firsttime'],
					'height'  => $figure['height'],
					'weight'  => $figure['weight'],
					'styleDY' => $styleDY ? $styleDY : 0,
					'souce_from'=>$serve_mode[$figure['service_mode']],
					'real_name'  => $real_name?$real_name:'',
					'phone'  => $phone?$phone:'',
					'serve_name'  => $serve_name?$serve_name:'',
					'serve_address'  => $serve_address?$serve_address:'',
					'store_mobile'  => $store_mobile?$store_mobile:'',
			);
			$_member_figure_mod = & m('customer_figure');
			$figure_info = $this->_figure_positions($figure);
			
			//获得风格特体
			$json_data = file_get_contents('http://api.figure.cotte.cn/soap/club.php?act=getSpecialFields');
			$json_data = json_decode($json_data,true);
			if(!empty($figure)) {
				//特殊体形
				if ($json_data['result']['data']['public']) {
					foreach ($json_data['result']['data']['public'] as $pvalue) {
						
						if ($figure[$pvalue['value_name']]) {
							foreach($pvalue['item'] as $ipvalue) {
								if($ipvalue['value'] == $figure[$pvalue['value_name']]) {
									$feature[] = array(
										'name' => $pvalue['cateName'],
										'val'  => $ipvalue['name'],
									);
									
								}
							}
						}
					}
				}
				//风格
				if ($json_data['result']['data']['special']) {
					foreach ($json_data['result']['data']['special'] as $svalue) {
						if ($figure[$pvalue['value_name']]) {
							foreach($svalue['item'] as $isvalue) {
								if($isvalue['value'] == $figure[$svalue['value_name']]) {
									$style[] = array(
										'name' => $svalue['cateName'],
										'val'  => $isvalue['name'],
									);
									
								}
							}
						
						}
					}
				}
			}
			
			$return = array(
					'statusCode' => 1,
					'result'     => array(
							'worker'   => $worker,
							'figure'   => $figure_info,
							'feature'  => $feature,
							'style'    => $style,
					),
			);
			return $json->encode($return);
		
		}
		
		//量体列表
		//http://local.soaapi.cotte.com/soap/shao.php?act=figure_list&token=eda6b5ebb68702e0e49841cb9aeb50d3&figure_sn=271
		function figure_list($data){
			global $json;
			$mem_mod          = m('member');
			$figure_mod       = m('customer_figure');
			$serve   = m('serve');
			$token     = isset($data->token) ? $data->token: '';
			$user_info = getUserInfo($token);
			$figure_sn=isset($data->figure_sn) ? $data->figure_sn: '';
			
			if (!$user_info) {
				$this->errorCode = 100;
				$this->msg = 'token参数错误';
				return $this->eresult();
			}
			if (!$figure_sn) {
				$this->errorCode = 101;
				$this->msg = '未传figure_sn';
				return $this->eresult();
			}
			$phone=$figure_mod->get(array(
					'conditions'=>"figure_sn='{$figure_sn}'",
					'fields'=>'customer_mobile,userid',
			));
			
			if($phone['customer_mobile']){
			/* $figure = $figure_mod->find(array(
					'conditions' =>"customer_mobile='{$phone['customer_mobile']}' AND figure_state=1 AND id_serve !=0 group by liangti_id,customer_mobile having max(figure_sn)",
					'fields'=>"figure_sn,liangti_id,id_serve,lasttime,liangti_name,service_mode",
					'order' =>"figure_sn DESC",
					//'index_key' =>"liangti_id",
			)); */
				
				$sql="SELECT figure_sn,liangti_id,id_serve,lasttime,liangti_name,service_mode FROM(select * from cf_customer_figure where customer_mobile = '{$phone['customer_mobile']}' and figure_state = 1 and id_serve !=0 ORDER BY figure_sn DESC) AS T 
                      GROUP BY T.liangti_id,T.customer_mobile ORDER BY T.figure_sn DESC";
				$db = &db();
				$r=$db->query($sql);
				$figure = array();
				while($row=@mysql_fetch_assoc($r)){
					$figure[]=$row;
				}
			}else{
				$this->errorCode = 104;
				$this->msg = '没有对应figure_sn';
				return $this->eresult();
			}
			if($figure)
			{
				$ins=0;
				foreach($figure as $key=>$val){
					
					$serves=$serve->get(array(
							'conditions' =>"idserve='{$val['id_serve']}'",
							'fields' =>"linkman,serve_name",
					));
					
					if(!empty($val['liangti_name'])){
						$figures[$ins]['name']=$val['liangti_name'];
					}else{
						if(!empty($val['liangti_id']))
						{
							$mems=$mem_mod->get(array(
									'conditions'=>"user_id='{$val['liangti_id']}'",
									'fields'=>"real_name",
							));
							if(!$mems['real_name']){
								if($val['service_mode']== 4){
									$figures[$ins]['name']=$val['liangti_name'];
								}else{
									$figures[$ins]['name']='';
								}
									
							}else{
								$figures[$ins]['name']=$mems['real_name'];
									
							}
						}else{
						if(!$serves['linkman']){
								$figures[$ins]['name']='';
							}else{
								$figures[$ins]['name']=$serves['linkman'];
							}
						}
					}
					
						if(!$serves['serve_name']){
							
							if($val['service_mode']== 4){
								
								$figures[$ins]['serve_name']='后台录入';
								
							}else{
								$figures[$ins]['serve_name']='';
							}
							
						}else{
							$figures[$ins]['serve_name']=$serves['serve_name'];
						}
					if(!$val['lasttime']){
						$figures[$ins]['lasttime']=0;
					}else{
					$figures[$ins]['lasttime']=date('Y-m-d H:i:s',$val['lasttime']);
					}
					$figures[$ins]['figure_sn']=$val['figure_sn'];
					$ins +=1;
				}
				
		}else{
			$this->errorCode = 104;
			$this->msg = '无量体数据';
			return $this->eresult();
		}
		
			$return = array(
					'statusCode' => 1,
					'result'     => array(
							'figure'   => $figures,
							
					),
			);
			return $json->encode($return);
				
		}
		
		//备注信息
		//http://local.soaapi.cotte.com/soap/shao.php?act=remarks&token=eda6b5ebb68702e0e49841cb9aeb50d3&figure_sn=271
		function remarks($data)
		{
			global $json;
			$remark_mod  =m('customerremark');
			$figure_mod       = m('customer_figure');
			$token     = isset($data->token) ? $data->token: '';
			$customer_name     = isset($data->customer_name) ? $data->customer_name: '';
			$customer_mobile     = isset($data->customer_mobile) ? $data->customer_mobile: '';
			$user_info = getUserInfo($token);
			$figure_sn=isset($data->figure_sn) ? $data->figure_sn: '';
			if (!$user_info) {
				$this->errorCode = 100;
				$this->msg = 'token参数错误';
				return $this->eresult();
			}
			if (!$figure_sn) {
				$this->errorCode = 101;
				$this->msg = '未传figure_sn';
				return $this->eresult();
			}
			
			$figures=$figure_mod->get(array(
					'conditions' =>"figure_sn='{$figure_sn}'",
					
			));
			
			if(!empty($figures['remark'])){
				$customer['customer_name']=$figures['remark'];
				$customer['customer_mobile']=$figures['remark_phone'];
			}else{
				$customer=$figure_mod->get(array(
						'conditions' =>"figure_sn='{$figure_sn}'",
						'fields' =>"customer_mobile,customer_name",
				));
			}
			if(!empty($customer_name) && !empty($customer_mobile)){
				$rema_data=array(
						'remark' =>$customer_name,
						'remark_phone' =>$customer_mobile,
				);
					
				$figure_mod->edit($figure_sn,$rema_data);
			}
			
			$return = array(
					'statusCode' => 1,
					'result'     => array(
							'customer'   => $customer,
					),
			);
			return $json->encode($return);
		}
		/**
		 * 顾客详情
		 *
		 * @param string $token 用户token;
		 *
		 * @access protected
		 * @author shaozizhen
		 */
		//http://local.soaapi.cotte.com/soap/shao.php?act=cuscontent&token=eda6b5ebb68702e0e49841cb9aeb50d3&figure_sn=1297
		//http://api.cotte.cn/soap/shao.php?act=cuscontent&token=364bd13a379dd2dd16dc74b37ba871e1&figure_sn=2269
		function cuscontent($data)
		{
			global $json;
			$figure_mod       = m('customer_figure');
			$remark_mod  =m('customerremark');
			$mem_mod          = m('member');
			$member_invite= m('memberinvite');
			$order_mod=m("order");
			$token     = isset($data->token) ? $data->token: '';
			$user_info = getUserInfo($token);
			$figure_sn=isset($data->figure_sn) ? $data->figure_sn: '';
			if (!$user_info) {
				$this->errorCode = 100;
				$this->msg = 'token参数错误';
				return $this->eresult();
			}
			if (!$figure_sn) {
				$this->errorCode = 101;
				$this->msg = '没有传量体数据id';
				return $this->eresult();
			}
			
			$phone=$figure_mod->get(array(
					'conditions'=>"figure_sn='{$figure_sn}'",
					'fields'=>'customer_mobile,userid,customer_name,remark,remark_phone',
			));
			
	
					$members=$mem_mod->get(array(
							'conditions' =>"user_id='{$phone['userid']}'",
							'fields'  =>"user_id,nickname,user_name,gender,birthday,last_login,final_amount_num,avatar,order_num",
					));
					
					if(!$members){
						$this->errorCode = 103;
						$this->msg = '该顾客未绑定会员';
						return $this->eresult();
					}
					
						
					
					if($phone['remark']){
						$members['nickname']=$phone['remark'] ? $phone['remark'] : '';
						$members['user_name']=$phone['remark_phone'] ? $phone['remark_phone'] : '';
					}else{
						$members['nickname']=$phone['customer_name'] ? $phone['customer_name'] : '';
						$members['user_name']=$phone['customer_mobile'] ? $phone['customer_mobile'] : '';
					}
					
					if(empty($members['birthday'])){
						$members['birthday']='未知';
					}
					
					if(!empty($members['last_login'])){
						$members['last_login']=date('Y-m-d',$members['last_login']);
					}else{
						$members['last_login']= '' ;
					}
					
					$invites=$member_invite->get(array(
							'conditions'=>"invitee='{$phone['userid']}'",
							'fields'  =>"add_time",
					));
					if(!empty($members['avatar'])){
					       $members['avatar']= LOCALHOST1.'/upload/avatar/'.$members['avatar'];
						}else{
							$members['avatar']='';
						}
					if(!empty($invites['add_time'])){
						$members['add_time']=date('Y-m-d',$invites['add_time']);
					}else{
						$members['add_time']=0;
					}
					
					$counts = $figure_mod->find(array(
							'conditions' =>"customer_mobile='{$phone['customer_mobile']}' AND figure_state=1 AND id_serve !=0 group by liangti_id,customer_mobile",
					        'fields'	 => "figure_sn",
					));
					$count=count($counts);
					if(!empty($count)){
						$members['state']=1;
						$members['liangti_num']=$count;
					}else{
						$members['state']=0;
						$members['liangti_num']=0;
					}
						
					if($members['user_id'])
					{
						$orders=$order_mod->get(array(
								'conditions' =>"user_id='{$members['user_id']}' AND (status = 40 or status = 30 or status = 60)",
								'fields'    =>"sum(final_amount) as num1,sum(money_amount) as num2,sum(coin) as num3,count(order_id) as count",
								'index_key' =>"",
						));
						if($orders)
						{
							$orders['num']=$orders['num1'] + $orders['num2'] + $orders['num3'];
							$members['final_amount_num']=$orders['num'] ? $orders['num'] : '0';
							$members['order_num']=$orders['count'] ? $orders['count'] : '0';
						}
					}
				
				$return = array(
						'statusCode' => 1,
						'result'     => array(
								'member'   => $members,
						),
				);
			
			
			return $json->encode($return);
		}
		//http://local.soaapi.cotte.com/soap/shao.php?act=cuslists_edit
		function cuslists_edit($data)
		{
			global $json;
			$member_invite=m("memberinvite");
			$customer=m("customer_figure");
			
			$member=m("member");
			
			$invites=$member_invite->find(array(
					'conditions' =>"1=1",
			));
			foreach($invites as $key=>$val)
			{
				$cuss=$customer->get(array(
						'conditions' =>"storeid='{$val['inviter']}' AND userid='{$val['invitee']}' AND type_cus <> 0",
				));
				
				if(empty($cuss))
				{
					$members=$member->get(array(
							'conditions' =>"user_id='{$val['invitee']}'",
							'fields' =>"user_name,nickname",
					));
					$custome_data=array(
							'storeid'  => $val['inviter'],
							'customer_mobile'  =>$members['user_name'] ,
							'userid' => $val['invitee'],
							'customer_name'    => $members['nickname'],
							'type_cus' => 2,
							'firsttime' => time(),
							'lasttime' => time(),
					);
					$sss=$customer->add($custome_data);
				}
			}
			$return = array(
					'statusCode' => 1,
					'result'     => array(
							'member'   => $sss,
					),
			);
				
				
			return $json->encode($return);
		}
		
		/**
		 * 我的顾客列表（推荐的下线）
		 *
		 * @param string $token 用户token;
		 *
		 * @access protected
		 * @author xuganglong <781110641@qq.com>
		 * @return void
		 */
		//2015-10-5 shaozizhen 修改列表加头像
		//http://local.soaapi.cotte.com/soap/shao.php?act=cus_list_new&token=eda6b5ebb68702e0e49841cb9aeb50d3
		//api.cotte.cn/soap/shao.php?act=cus_list_new&token=364bd13a379dd2dd16dc74b37ba871e1
		function cus_list_new($data)
		{
			global $json;
			$token     = isset($data->token) ? $data->token: '';
			$pageSize  = isset($data->pageSize) ? $data->pageSize : 20;
			$pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
			$user_info = getUserInfo($token);
			$figureorderm_mod = m('figureorderm');
			$figure_mod       = m('customer_figure');
			$remark_mod  =m('customerremark');
			$mem_mod          = m('member');
			$order_mod        = m('order');
			
			if (!$user_info) {
				$this->errorCode = 100;
               $this->msg = '账号不存在';
               return $this->eresult();
			}
			
			if ($user_info['serve_type'] != 1) {
				$this->errorCode = 101;
				$this->msg = '必须当前创业者才能查看自己的顾客';
				return $this->eresult();
			}
		
			$user_id  = $user_info['user_id'];
			$limit    = ($pageSize * ($pageIndex-1)) . ','. $pageSize;
			
			$mem_list = $mem_mod->find(array(
					'conditions'=> 'member_invite.inviter ='.$user_id,
					'join'      => 'has_member_invite',
					'fields'    => 'member.user_id, member.user_name, member.nickname as customer_name,member.avatar',
					'order'     => 'add_time DESC',
					'index_key' => '',
			));
			$invitee_arr = array();
			foreach($mem_list as $vkey=>$valm) {
				$invitee_arr[] = $valm['user_name'];
			}
			
			$figure_all = $figure_mod->find(array(
					'conditions' => "storeid = '{$user_id}' AND type_cus <> 0 AND userid <> '{$user_id}' AND customer_name <> ''",
					'fields'	 => "figure_sn, customer_mobile,customer_name,lasttime,storeid,userid,remark,remark_phone",
					'order'      => "figure_sn DESC",
					//'limit'	     => $limit,
			        'index_key'  => "",
			));
			
			foreach($figure_all as $key=>$val){
				if(in_array($val['customer_mobile'],$invitee_arr)){
					$figure_all[$key]['figure_type']=invite;
					$figure_all[$key]['type']=1;
		
					$avatar=$mem_mod->get(array(
							'conditions' =>"user_id='{$val['userid']}'",
							'fields'=>"user_id,avatar",
					));
					if(!$avatar)
					{
						$keys[$key]=$key;
					}
					if(!empty($avatar['avatar'])){
						$figure_all[$key]['avatar']=LOCALHOST1.'/upload/avatar/'.$avatar['avatar'];
					}else{
						
						$figure_all[$key]['avatar']='';
					}
					
				}else{
				
					/* if($val['userid'] != 0)
					{
						$members=$mem_mod->get(array(
								'conditions' =>"user_id='{$val['userid']}'",
						));
						
						 if(!$members)
						{
							$keys[$key]=$key;
						
						} 
						
					} */
					$figure_all[$key]['figure_type']=add;
					$figure_all[$key]['type']=0;
					$figure_all[$key]['avatar']='';
				}
				if($val['remark']){
					$figure_all[$key]['customer_name']=$val['remark'];
					$figure_all[$key]['customer_mobile']=$val['remark_phone'];
				}else{
					$figure_all[$key]['remark']='';
					$figure_all[$key]['remark_phone']='';
				}
				
				
			}
			
		/* 	 if($keys)
			{
				foreach($keys as $k=>$v)
				{
					unset($figure_all[$v]);
				}
			}  */
		    
			
			$return = array(
				'statusCode' => 1,
				'result'     => array(
					'data'  => $figure_all,
				)
			);
			return $json->encode($return);
		}
		
		//顾客绑定会员
		//http://local.soaapi.cotte.com/soap/shao.php?act=adcus
		/* function adcus($data){
			global $json;
			$figure_mod       = m('customer_figure');
			$mem_mod          = m('member');
			$member_invite= m('memberinvite');
			$figure=$figure_mod->find(array(
					'conditions'=>"1=1",
					'fields'  =>"customer_mobile,userid,figure_sn",
			));
			foreach($figure as $key=>$val){
				$mem=$mem_mod->get(array(
						'conditions'=>"user_name='{$val['customer_mobile']}' AND serve_type <> 2",
						'fields' =>"user_id",
				));
				if($val['store_id']){
					$mee=$mem_mod->get(array(
							'conditions'=>"user_id='{$val['store_id']}'",
							'fields' =>"nickname",
					));
					if($mem){
						if($mem && !$val['userid']){
							$datt=array(
									'userid'=>$mem['user_id'],
							);
							$dd=array(
									'inviter'  => $val['store_id'], //邀请人
									'invitee'  => $mem['user_id'],
									'nickname' => $mee['nickname'],     //邀请人昵称
									'type'      => 0,
									'add_time' => time(),
									'come_from'=>'app|addcustom',
							);
							$inve=$member_invite->get(array(
									'conditions'=>"invitee='{$mem['user_id']}'",
							));
							if($inve){
									
							}else{
								$figure_mod->edit("figure_sn='{$val['figure_sn']}'",$datt);
								$member_invite->add($dd);
							}
								
								
						}
					}
					
				}
				
				
			}
			$return = array(
					'statusCode' => 1,
					
			);
			return $json->encode($return);
		} */
		/**
		 * 我的顾客对应量体信息（下线用户）=== 暂时屏蔽
		 *
		 * @param string $token 用户token;
		 *
		 * @access protected
		 * @author xuganglong <781110641@qq.com>
		 * @return void
		 
		public function check_new($data)
		{
			global $json;
			$db = db();
		    $figure_sn = isset($data->figure_sn) ? $data->figure_sn: '';
			$token     = isset($data->token) ? $data->token: '';
			$user_info = getUserInfo($token);
			
			$figureorderm_mod = m('figureorderm');
			$meminvite_mod    = m('memberinvite');
			
			if (!$user_info) {
				$this->errorCode = 100;
				$this->msg = '账号不存在';
				return $this->eresult();
			}
			if ($user_info['serve_type'] != 1) {
				$this->errorCode = 101;
				$this->msg = '必须当前创业者才能查看自己的顾客';
				return $this->eresult();
			}
			if(!$figure_sn) {
				$this->errorCode = 101;
				$this->msg = '没有要查看的客户量体数据！';
				return $this->eresult();
			}
			
			$figure  = $figureorderm_mod->get($figure_sn);
			$figure_info_num = $figureorderm_mod->get(array(
				'conditions' => " userid = {$figure['userid']}",
				'join'       => 'has_member',
				'fields'	 => "figure_order_m.id as figure_sn, figure_order_m.liangti_state, member.nickname as customer_name, figure_order_m.phone as customer_mobile, count(id) as order_num ",
				'order'	     => 'id DESC',
				'index_key'	 => '',
			));
			//获得下级信息
			$meminvite = $meminvite_mod->get(array(
				'conditions'=> "inviter={$user_info['user_id']} and invitee={$figure['userid']} ",
			));
			if(!$meminvite) {
				$this->errorCode = 101;
				$this->msg = '客户不属于该创业者';
				return $this->eresult();
			}

			$worker = array(
				'customer_name'   => !empty($figure_info_num['customer_name']) ?  $figure_info_num['customer_name'] : '',
				'customer_mobile' => $figure_info_num['customer_mobile'],
				'order_num'       => $figure_info_num['order_num'],
				'lasttime'        => $figure['modi_time'],
				'firsttime'       => $figure['time'],
				'height'          => $figure['lheight'],
				'weight'          => $figure['lweight'],
			);

			//处理量体数据显示
			$figure_info = $this->_figure_positions($figure);
			
			$style   = array();//风格
			$feature = array();//特体
			//获得风格特体
			$json_data = file_get_contents(PROJECT_PATH.'includes/data/config/size_json/figure.json');
			$json_data = json_decode($json_data, true);
			if(!empty($figure)) {
				//特殊体形
				if ($json_data['public']) {
					foreach ($json_data['public'] as $pvalue) {
						if ($figure[$pvalue['value_name']]) {
							foreach($pvalue['item'] as $ipvalue) {
								if($ipvalue['value'] == $figure[$pvalue['value_name']]) {
									$feature[] = array(
										'name' => $pvalue['cateName'],
										'val'  => $ipvalue['name'],
									);
									
								}
							}
						}
					}
				}

				//风格
				if ($json_data['special']) {
					foreach ($json_data['special'] as $svalue) {
						if ($figure[$pvalue['value_name']]) {
							foreach($svalue['item'] as $isvalue) {
								if($isvalue['value'] == $figure[$svalue['value_name']]) {
									$style[] = array(
										'name' => $svalue['cateName'],
										'val'  => $isvalue['name'],
									);
									
								}
							}
						}
					}
				}
			}
			
			$return = array(
				'statusCode' => 1,
				'result'     => array(
					'worker'   => $worker,
					'figure'   => $figure_info,
					'feature'  => $feature,
					'style'    => $style,
				),
			);
			return $json->encode($return);
		}*/
		
		/**
		 * 处理量体数据返回像   22项量体信息
		 */
		public function _figure_positions($figure)
		{
			$_mod_member_figure = m('customer_figure');
			$_figure = $_mod_member_figure->_positions();
			$unit = $figure['unit'];
		    $wunit = 'kg';
			
			//重新处理返回数据
			$figure_info = array(
				array('name' => $_figure['lw']['zname'], 'val' => !empty($figure['lw']) ? $figure['lw'].$unit: '0'.$unit),
				array('name' => $_figure['xw']['zname'], 'val' => !empty($figure['xw']) ? $figure['xw'].$unit: '0'.$unit),
				array('name' => $_figure['zyw']['zname'], 'val' => !empty($figure['zyw']) ? $figure['zyw'].$unit: '0'.$unit),
				array('name' => $_figure['tw']['zname'], 'val' => !empty($figure['tw']) ? $figure['tw'].$unit: '0'.$unit),
				array('name' => $_figure['zww']['zname'], 'val' => !empty($figure['zww']) ? $figure['zww'].$unit: '0'.$unit),
				array('name' => $_figure['yww']['zname'], 'val' => !empty($figure['yww']) ? $figure['yww'].$unit: '0'.$unit),
				array('name' => $_figure['sbw']['zname'], 'val' => !empty($figure['sbw']) ? $figure['sbw'].$unit: '0'.$unit),
				array('name' => $_figure['zjk']['zname'], 'val' => !empty($figure['zjk']) ? $figure['zjk'].$unit: '0'.$unit),
				//array('name' => $_figure['zxc']['zname'], 'val' => !empty($figure['zxc']) ? $figure['zxc'].$unit: '0'.$unit),
				//array('name' => $_figure['yxc']['zname'], 'val' => !empty($figure['yxc']) ? $figure['yxc'].$unit: '0'.$unit),
				array('name' => $_figure['qjk']['zname'], 'val' => !empty($figure['qjk']) ? $figure['qjk'].$unit: '0'.$unit),
				array('name' => $_figure['hyjc']['zname'], 'val' => !empty($figure['hyjc']) ? $figure['hyjc'].$unit: '0'.$unit),
				//array('name' => $_figure['hyc']['zname'], 'val' => !empty($figure['hyc']) ? $figure['hyc'].$unit: '0'.$unit),
				array('name' => $_figure['qyj']['zname'], 'val' => !empty($figure['qyj']) ? $figure['qyj'].$unit: '0'.$unit),
				array('name' => $_figure['yw']['zname'], 'val' => !empty($figure['yw']) ? $figure['yw'].$unit: '0'.$unit),
				array('name' => $_figure['tgw']['zname'], 'val' => !empty($figure['tgw']) ? $figure['tgw'].$unit: '0'.$unit),
				array('name' => $_figure['td']['zname'], 'val' => !empty($figure['td']) ? $figure['td'].$unit: '0'.$unit),
				array('name' => $_figure['hyg']['zname'], 'val' => !empty($figure['hyg']) ? $figure['hyg'].$unit: '0'.$unit),
				array('name' => $_figure['qyg']['zname'], 'val' => !empty($figure['qyg']) ? $figure['qyg'].$unit: '0'.$unit),
				array('name' => $_figure['zkc']['zname'], 'val' => !empty($figure['zkc']) ? $figure['zkc'].$unit: '0'.$unit),
				array('name' => $_figure['ykc']['zname'], 'val' => !empty($figure['ykc']) ? $figure['ykc'].$unit: '0'.$unit),
				array('name' => $_figure['xiw']['zname'], 'val' => !empty($figure['xiw']) ? $figure['xiw'].$unit: '0'.$unit),
				array('name' => $_figure['jk']['zname'], 'val' => !empty($figure['jk']) ? $figure['jk'].$unit: '0'.$unit),
				array('name' => $_figure['dkzkc']['zname'], 'val' => !empty($figure['dkzkc']) ? $figure['dkzkc'].$unit: '0'.$unit),
				array('name' => $_figure['dkykc']['zname'], 'val' => !empty($figure['dkykc']) ? $figure['dkykc'].$unit: '0'.$unit),
				array('name' => $_figure['syzxc']['zname'], 'val' => !empty($figure['syzxc']) ? $figure['syzxc'].$unit: '0'.$unit),
				array('name' => $_figure['cyzxc']['zname'], 'val' => !empty($figure['cyzxc']) ? $figure['cyzxc'].$unit: '0'.$unit),
				array('name' => $_figure['dyzxc']['zname'], 'val' => !empty($figure['dyzxc']) ? $figure['dyzxc'].$unit: '0'.$unit),
				array('name' => $_figure['syyxc']['zname'], 'val' => !empty($figure['syyxc']) ? $figure['syyxc'].$unit: '0'.$unit),
				array('name' => $_figure['cyyxc']['zname'], 'val' => !empty($figure['cyyxc']) ? $figure['cyyxc'].$unit: '0'.$unit),
				array('name' => $_figure['dyyxc']['zname'], 'val' => !empty($figure['dyyxc']) ? $figure['dyyxc'].$unit: '0'.$unit),
				array('name' => $_figure['syhyc']['zname'], 'val' => !empty($figure['syhyc']) ? $figure['syhyc'].$unit: '0'.$unit),
				array('name' => $_figure['cyhyc']['zname'], 'val' => !empty($figure['cyhyc']) ? $figure['cyhyc'].$unit: '0'.$unit),
				array('name' => $_figure['dyhyc']['zname'], 'val' => !empty($figure['dyhyc']) ? $figure['dyhyc'].$unit: '0'.$unit),
				array('name' => $_figure['lheight']['zname'], 'val' => !empty($figure['height']) ? $figure['height'].$unit: '0'.$unit),
				array('name' => $_figure['lweight']['zname'], 'val' => !empty($figure['weight']) ? $figure['weight'].$wunit: '0'.$wunit),
			
			
			);
			return $figure_info;
		}
		
		/**
		 * 根据品类id获得对应体形及风格
		 */
		public function  _get_body_type($clothId){
			$_mod_mtm_bt = &m("mtmbodytype");
			$body_type_tm = $_mod_mtm_bt->find("clothId = '{$clothId}'");
			foreach ($body_type_tm as $row){
				$body_type['style'][$row['clothId']]['info']['name'] = $row['clothName'];
				$body_type['style'][$row['clothId']]['info']['id']   = $row['cateID'];
				$body_type['style'][$row['clothId']]['info']['nm']   = 'body_type_'.$row['clothId'];
				$body_type['style'][$row['clothId']]['list'][$row['id']] = $row;
		
			}
			$body_type_ts = $_mod_mtm_bt->find("clothId = '0'");
		
			foreach ($body_type_ts as $row){
		
				$body_type['feature'][$row['cateID']]['info']['name'] = $row['cateName'];
				$body_type['feature'][$row['cateID']]['info']['id']   = $row['cateID'];
				$body_type['feature'][$row['cateID']]['info']['nm']   = 'body_type_'.$row['cateID'];
				$body_type['feature'][$row['cateID']]['list'][$row['id']] = $row;
			}
			
			return $body_type;
		}
		
	
	/**
     * 添加顾客
     */
		//http://local.soaapi.cotte.com/soap/shao.php?act=addcustom&token=eda6b5ebb68702e0e49841cb9aeb50d3&customer_name=zhenzhen&customer_mobile=15092283823
	public function addcustom($data)
	{
   	    global $json;
   	    $token 		= isset($data->token) ? $data->token : '' ;
		$customer_name   = isset($data->customer_name) ? $data->customer_name : '' ;
		$customer_mobile = isset($data->customer_mobile) ? $data->customer_mobile : 0 ;
   	    $customer_mod    = m('customer_figure');
   	    $mod          = m('member');
   	    $member_invite= m('memberinvite');
   	    $user_info  = getUserInfo($token);
   	   
		if(!$user_info)
		{
			$this->errorCode = 100;
		  	$this->msg = '账号不存在';
		  	return $this->eresult();
		}
		
		if ($user_info['serve_type'] != 1)
		{
			$this->errorCode = 101;
			$this->msg = '必须当前创业者才能查看自己的顾客';
			return $this->eresult();
		}
		
		if(!$customer_name)
		{
			$this->errorCode = 102;
		  	$this->msg = '顾客姓名不能为空';
		  	return $this->eresult();
		}
	
		if(!$customer_mobile)
		{
			$this->errorCode = 103;
			$this->msg = '手机号不能为空';
			return $this->eresult();
		}
		if(!preg_match('/^1[3458][0-9]{9}$/', $customer_mobile))
		{
			$this->errorCode = 104;
			$this->msg = '手机号格式错误';
			return $this->eresult();
	   	}
	   	$storeid=$user_info['user_id'];
	   	
	   	$type_cus=1;
	   	if (!$customer_mod->unique($customer_mobile,$storeid,$type_cus))
	   	{	
	   		$this->errorCode = 105;
	   		$this->msg = '手机号已存在!';
	   		return $this->eresult();
	   	}
	   $mems=$mod->get(array(
	   		'conditions' =>"serve_type <> 2 AND user_name='{$customer_mobile}'",
	   		'fields'  =>"user_id",
	   ));
      if($mems){
	      $invites=$member_invite->get(array(
			'conditions'=>"invitee='{$mems['user_id']}'",
	       ));
	     if($invites){
	     	$this->errorCode = 106;
	     	$this->msg = '该顾客已被邀请过!';
	     	return $this->eresult();
	       }
	       /* else{
		    $invarry=array(
				'inviter'  => $storeid, //邀请人
				'invitee'  => $mems['user_id'],
				'nickname' => $user_info['nickname'],     //邀请人昵称
				'type'      => 0,
				'add_time' => time(),
				'come_from'=>'app|addcustom',
		      );
		    $member_invite->add($invarry);
	     } */
       }
	 
	   	$data=array(
	   			'customer_name' => $customer_name,
	   			'customer_mobile' =>$customer_mobile,
	   			'storeid' =>$user_info['user_id'],
	   			'type_cus' =>1,
	   			'firsttime' => time(), 
	   	);
	   	if($mems && !$invites){
	   	$data['userid']=$mems['user_id'];
	   	}
	   	$cusmod=$customer_mod->add($data);
	   
	   	if($cusmod === false){
	   		$this->msg = '添加失败!';
	   		return $this->eresult();
	   		
	   	}else{
	   		return $this->sresult();
	   	}
	   	
	}

	/**
     * 修改顾客信息
     */
	function editcustom($data)
	{
		global $json;
		$token 		= isset($data->token) ? $data->token : '' ;
		$figure_sn 		= isset($data->figure_sn) ? $data->figure_sn : '' ;
		$customer_mod    = m('customer_figure');
		$mod          = m('member');
		$user_info  = getUserInfo($token);
	
		if(!$user_info)
		{
			$this->errorCode = 100;
			$this->msg = '账号不存在';
			return $this->eresult();
		}
	
		if ($user_info['serve_type'] != 1)
		{
			$this->errorCode = 101;
			$this->msg = '必须当前创业者才能修改自己的顾客';
			return $this->eresult();
		}
		if(!$figure_sn){
			$this->errorCode = 101;
			$this->msg = '没有顾客id';
			return $this->eresult();
		}
		
		$cust=$customer_mod->get(array(
				'conditions' => "figure_sn='{$figure_sn}'",
		));
		/*
		if($cust['storeid'] !=$user_info['user_id']){
			$this->errorCode = 101;
			$this->msg = '创业者没有这个顾客';
			return $this->eresult();
		}
		*/
		if ($data->customer_name) {//顾客姓名
			$data_c['customer_name'] = $data->customer_name;
		}
		
		if ($data->customer_mobile) {//顾客电话
			$data_c['customer_mobile'] = $data->customer_mobile;
		}
		
		if($data_c['customer_mobile'])
		{
			
		if(!preg_match("/1[34587]{1}\d{9}$/", $data_c['customer_mobile']))
		{
			$this->errorCode = 101;
			$this->msg = '手机号格式错误';
			
			return $this->eresult();
			
		}
		
		$storeid=$user_info['user_id'];
		
		if (!$customer_mod->unique($data_c['customer_mobile'],$storeid,$figure_sn))
		{
			$this->msg = '手机号已存在!';
			return $this->eresult();
		}
		}
		
		$cusmod=$customer_mod->edit($figure_sn,$data_c);
		if($cusmod === false){
			$this->msg = '修改失败!';
			return $this->eresult();
			
		}else{
			return $this->sresult();
		}
		 
	}
	/**
	 *  删除顾客
	 */
	//http://local.soaapi.cotte.com/soap/shao.php?act=dropcustom&token=eda6b5ebb68702e0e49841cb9aeb50d3&figure_sn=279
	public function dropcustom($data)
	{
		global $json;
		$token 		= isset($data->token) ? $data->token : '' ;
		$figure_sn 		= isset($data->figure_sn) ? $data->figure_sn : '' ;
		$customer_mod    = m('customer_figure');
		$member_invite= m('memberinvite');
		$mem= m('member');
		$user_info  = getUserInfo($token);
		if(!$user_info)
		{
			$this->errorCode = 100;
			$this->msg = '账号不存在';
			return $this->eresult();
		}
	
		if ($user_info['serve_type'] != 1)
		{
			$this->errorCode = 101;
			$this->msg = '必须创业者才能删除自己的顾客';
			return $this->eresult();
		}
		if(!$figure_sn){
			$this->errorCode = 102;
			$this->msg = '没有顾客标识';
			return $this->eresult();
		}
		//invitee被邀请人 inviter邀请人
		
		
		$cust=$customer_mod->get(array(
				'conditions' => "figure_sn='{$figure_sn}'",
		));
		
		if($cust['storeid'] !=$user_info['user_id']){
			$this->errorCode = 103;
			$this->msg = '创业者没有这个顾客';
			return $this->eresult();
		}
		if($cust['userid']){
			$invite=$member_invite->get(array(
					'conditions'  =>"invitee = '{$cust['userid']}' AND inviter='{$user_info['user_id']}'",
			));
			if($invite){
				$this->errorCode = 104;
				$this->msg = '顾客与创业者是绑定关系';
				return $this->eresult();
			}
		}
		$cusmod=$customer_mod->drop($figure_sn);
	
		if($cusmod === false){
			$this->msg = '删除失败!';
			return $this->eresult();
			
		}
			return $this->sresult();
		 
	}
	//调关于我们页面
	function about($data){
		$http=LOCALABOUT."/article/about.html";
		
		   $this->result = $http;
	      return $this->sresult();
	}
	

	/**
	 * 地址管理列表
	 */
	 function addrlist($data)
	{
	
		global $json;
		$m       = m('member');
		$address = m('address');
		$token     = isset($data->token) ? $data->token: '';
		$user_info = getToken($token);
		
		if(empty($user_info)) {
			$this->errorCode = 100;
			$this->msg = '账号不存在';
			return $this->eresult();
		}
		if ($user_info['serve_type'] != 1)
		{
			$this->errorCode = 101;
			$this->msg = '您不是创业者无权查看地址';
			return $this->eresult();
		}
	
		$user_id      = $user_info['user_id'];
		$address_list = $address->find(array(
				'join'       => 'belongs_to_member',
				'conditions' => 'address.user_id='.$user_id,
				'fields'     => 'address.*',
				'order'      => 'addr_id DESC',//综合排序
				'index_key'	 => '',
	
		));
		//默认地址
		$def_addr = 0;
		if($user_info['def_addr']) {
			$def_addr = $user_info['def_addr'];
		} else {
			$end_id = current($address_list);
			$def_addr = $end_id['addr_id'];
		}
	
		if($address_list){
			foreach($address_list as $key=>$val) {
				
				if($val['addr_id'] == $def_addr) {
					$address_list[$key]['def_addr'] = 1;
				}else{
					$address_list[$key]['def_addr'] = 0;
				}
				if($val['region_id']) {
					$region_mod = m('region');;
					$region_list = $region_mod->find(array(
							'conditions' => db_create_in($val['region_id'], 'region_id'),
							'fields'     => 'region_name',
							'index_key'  => '',
					));
					
					$region_name_arr = array();
					$region_name_str = array();
					if($region_list) {
						foreach($region_list as $v ) {
							$region_name_arr[] = $v['region_name'];
						}
						$region_name_str = implode(",", $region_name_arr);
					}
					$address_list[$key]['region_name'] = !empty($region_name_str) ? $region_name_str : '';
				}
			}
		}
	
	
		 $this->result = $address_list;
	      return $this->sresult();
	}
	
	
	
	/**
	 * 添加地址
	 *
	 */
	public function addAddress($data)
	{
		global $json;
		$add_mod = m('address');
		$m_mod   = m('member');
		$region   = m('region');
		$token     = isset($data->token) ? $data->token: '';
		$consignee    = isset($data->consignee) ? $data->consignee : '';
		$region_id   =isset($data->region_id) ? $data->region_id : '';//市级id
		$address     = isset($data->address) ? $data->address : '';
		$phone_mob    = isset($data->region_id) ? $data->phone_mob : '';
		$is_def      = isset($data->is_def) ? $data->is_def : 0;//是否默认 1:设为默认 0：不设为默认
		$user_info   = getToken($token);
		if(!$user_info) {
			$this->errorCode = 100;
			$this->msg = '账号不存在';
			return $this->eresult();
		} 

		if(!$consignee){
			$this->errorCode = 101;
			$this->msg = '收件人姓名不能为空';
			return $this->eresult();
		}
		if(!$region_id){
			$this->errorCode = 101;
			$this->msg = '没有最后一个地区的id';
			return $this->eresult();
		}
		 
		if(!$address){
			$this->errorCode = 101;
			$this->msg = '地址不能为空';
			return $this->eresult();
		}
		
		if(!$phone_mob){
			$this->errorCode = 101;
			$this->msg = '手机号不能为空';
			return $this->eresult();
		}
		if(!preg_match("/1[34587]{1}\d{9}$/",$phone_mob))
		{
			$this->msg = '请输入11位手机号';
			return $this->eresult();
		}
		
		if(!in_array($is_def,array(0,1))){
			$this->msg = '参数只为1或0';
			return $this->eresult();
		}
		$regions=$region->get(array(
				'conditions' =>"region_id='{$region_id}'",
		));
		
		if($regions['is_serve'] == 0){
			$region_name=$region->get(array(
			'conditions'=>"region_id='{$regions['parent_id']}'",
			));
			if($region_name['parent_id'] == 0){
			$region_names=$region_name['region_name'].' '.$regions['region_name'];
			}else{
				$region_na=$region->get(array(
						'conditions'=>"region_id='{$region_name['parent_id']}'",
				));
				$region_names=$region_na['region_name'].' '.$region_name['region_name'].' '.$regions['region_name'];
			}
		}else{
			$this->msg = '不是最后一个地区id';
			return $this->eresult();
		}
		$region_ids=$region_na['region_id'].','.$region_name['region_id'].','.$regions['region_id'];
		$user_id  = $user_info['user_id'];
	           $arr=array(
	           		'user_id' => $user_id,
			        'consignee' => $consignee,
	           		'region_id' => $region_ids,
	           		'region_name' => $region_names,
	           		'address' => $address,
	           		'phone_mob' => $phone_mob,
	              );
			$arr['user_id'] = $user_id;
		 
			$arrs=$add_mod->add($arr);
			//如果设为默认地址
			if($is_def == 1) {
				$res=$m_mod->edit($user_id, array("def_addr" => $arrs));
				if(!$res){
					$this->msg = '添加默认地址失败';
					return $this->eresult();
				}else{
					$this->msg ='添加默认地址成功';
					return $this->sresult();
				}
			}
			if($arrs){
				$this->msg ='添加成功';
				return $this->sresult();
			}else{
				$this->msg = '添加失败';
				return $this->eresult();
			}
			
	}
	
	/**
	 * 修改收获地址
	 *
	 */
	public function editAddress($data)
	{
		global $json;
		$add_mod = m('address');
		$m_mod   = m('member');
		$region   = m('region');
		$token     = isset($data->token) ? $data->token: '';
		$addr_id     = isset($data->addr_id) ? $data->addr_id: '';
		$user_info   = getToken($token);
		if(!$user_info) {
			$this->errorCode = 100;
			$this->msg = '账号不存在';
			return $this->eresult();
		}
       	if(!$addr_id){
		  $this->errorCode = 101;
		  $this->msg = '没有地址id';
		  return $this->eresult();
	    }
	    
	    $addr_reg=$add_mod->get(array(
	    		'conditions' =>"addr_id={$addr_id}",
	    ));
	    if($addr_reg['user_id'] != $user_info['user_id']){
	    	$this->errorCode = 101;
	    	$this->msg = '此地址不是该创业者的';
	    	return $this->eresult();
	    }
	   
		if ($data->consignee) {
			$data_a['consignee'] = $data->consignee;
		}
		if ($data->region_id) {
			$data_a['region_id'] = $data->region_id;
		}
		if ($data->address) {
			$data_a['address'] = $data->address;
		}
		if ($data->phone_mob) {
			$data_a['phone_mob'] = $data->phone_mob;
		}
		if ($data->is_def) {
			$data_m['is_def'] = $data->is_def;
		}
		
		if($data_a['region_id']){
			
			if(!$data_a['region_id']){
				
				$this->errorCode = 101;
				$this->msg = '没有最后一个地区的id';
				return $this->eresult();
			}
			
			$regions=$region->get(array(
					'conditions' =>"region_id='{$data_a['region_id']}'",
			));
			
			if($regions['is_serve'] == 0){
				$region_name=$region->get(array(
						'conditions'=>"region_id='{$regions['parent_id']}'",
				));
				if($region_name['parent_id'] == 0){
					$region_names=$region_name['region_name'].' '.$regions['region_name'];
				}else{
					$region_na=$region->get(array(
							'conditions'=>"region_id='{$region_name['parent_id']}'",
					));
					$data_a['region_name']=$region_na['region_name'].' '.$region_name['region_name'].' '.$regions['region_name'];
				}
			}else{
				$this->msg = '不是最后一个地区id';
				return $this->eresult();
			}
			$data_a['region_id']=$region_na['region_id'].','.$region_name['region_id'].','.$regions['region_id'];
		}
		
		if($data_a['address']){	
			
		if(!$data_a['address']){
			$this->errorCode = 101;
			$this->msg = '地址不能为空';
			return $this->eresult();
		}
		}
		
		if($data_a['phone_mob']){
		if(!$data_a['phone_mob']){
			$this->errorCode = 101;
			$this->msg = '手机号不能为空';
			return $this->eresult();
		}
		
		if(!preg_match("/1[34587]{1}\d{9}$/",$data_a['phone_mob']))
		{
			$this->msg = '请输入11位手机号';
			return $this->eresult();
		}
		}
		
		$user_id  = $user_info['user_id'];
		//如果设为默认地址
		if($data_m['is_def']){
		if(!in_array($data_m['is_def'],array(0,1))){
			$this->msg = '参数只为1或0';
			return $this->eresult();
		}
		if($data_m['is_def'] == 1) {
			$res=$m_mod->edit($user_id, array("def_addr" => $addr_id));
			if(!$res){
				$this->msg = '添加默认地址失败';
				return $this->eresult();
			}
		}
		}
		$data_a['user_id'] = $user_id;
		$arrs=$add_mod->edit($addr_id,$data_a);
		if($arrs){
			$this->msg ='添加成功';
			return $this->sresult();
		}else{
			$this->msg = '添加失败';
			return $this->eresult();
		}
	}
	/**
	 * 删除收获地址
	 *
	 */
	public function dropAddress($data)
	{
		global $json;
		$add_mod = m('address');
		$m_mod = m('member');
		$token     = isset($data->token) ? $data->token: '';
		$addr_id     = isset($data->addr_id) ? $data->addr_id: '';
		$user_info   = getToken($token);
		if(!$user_info) {
			$this->errorCode = 100;
			$this->msg = '账号不存在';
			return $this->eresult();
		}
		if(!$addr_id){
			$this->errorCode = 101;
			$this->msg = '没有地址id';
			return $this->eresult();
		}
		 
		$addr_reg=$add_mod->get(array(
				'conditions' =>"addr_id={$addr_id}",
		));
		if($addr_reg['user_id'] != $user_info['user_id']){
			$this->errorCode = 101;
			$this->msg = '此地址不是该创业者的';
			return $this->eresult();
		}
	    if($user_info['def_addr'] == $addr_id){
	    
		$nex_addr=$add_mod->get(array(
				'conditions' =>"user_id='{$user_info['user_id']}' AND addr_id <> '{$addr_id}'" ,
				'order' => "addr_id DESC",
		));
		
		$res=$m_mod->edit($user_info['user_id'], array("def_addr" => $nex_addr['addr_id']));
	}
	
	
		$res = $add_mod->drop("addr_id=$addr_id");
		if($res=== false){
			$this->msg = '地址删除失败';
			return $this->eresult();
		
		}else{
			$this->msg ='地址删除成功';
			return $this->sresult();
		}
	
	}
	function regionlist($data)
	{
		global $json;
		$add_mod = m('address');
		$redion_m = m('region');
		$reg_1  =isset($data->reg_1) ? $data->reg_1: '';
		$list1 = $redion_m->get_options(2);
		if($reg_1){
			if(!$list1[$reg_1]){
				$this->msg = '您给的所选第一级地址不正确';
				return $this->eresult();
			}
			$list2 = $redion_m->get_options($reg_1);
		}
		$return = array(
				'statusCode' => 1,
				'result'     => array(
						'list1' => $list1,
						'list2'   => $list2,
				),
		);
		return $json->encode($return);
			
	}
/* 	function cus_list_new($data)
	{
		global $json;
		$token     = isset($data->token) ? $data->token: '';
		$pageSize  = isset($data->pageSize) ? $data->pageSize : 20;
		$pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
		$user_info = getUserInfo($token);
		$figureorderm_mod = m('figureorderm');
		$figure_mod       = m('customer_figure');
		$remark_mod  =m('customerremark');
		$mem_mod          = m('member');
		$order_mod        = m('order');
			
		if (!$user_info) {
			$this->errorCode = 100;
			$this->msg = '账号不存在';
			return $this->eresult();
		}
			
		if ($user_info['serve_type'] != 1) {
			$this->errorCode = 101;
			$this->msg = '必须当前创业者才能查看自己的顾客';
			return $this->eresult();
		}
	
		$user_id  = $user_info['user_id'];
		$limit    = ($pageSize * ($pageIndex-1)) . ','. $pageSize;
		$mem_list = $mem_mod->find(array(
				'conditions'=> 'member_invite.inviter ='.$user_id,
				'join'      => 'has_member_invite',
				'fields'    => 'member.user_id, member.user_name, member.nickname as customer_name',
				'order'     => 'add_time DESC',
				'index_key' => '',
		));
			
		$invitee_arr = array();
		foreach($mem_list as $vkey=>$valm) {
			$invitee_arr[] = $valm['user_name'];
		}
			
		/*
		 // 这里需要 筛选出 最晚更新的那条量体数据
		$figure_all_add = $figure_mod->findAll(array(
				'conditions' => "storeid = {$user_id}",
				'fields'	 => "figure_sn, customer_mobile, lasttime",
				'order'      => "lasttime desc ",
				'index_key'  => "",
		));
			
		$figure_all_in = $figure_mod->findAll(array(
				'conditions' => db_create_in($invitee_arr, 'customer_mobile'),
				'fields'	 => "figure_sn, customer_mobile, lasttime",
				'order'      => "lasttime desc ",
				'index_key'  => "",
		));
		*/
/* 	
		if($invitee_arr) {
			$conditions = ' or '.db_create_in($invitee_arr, 'customer_mobile');
		}
			
		// 这里需要 筛选出 最晚更新的那条量体数据
		$figure_all = $figure_mod->findAll(array(
				'conditions' => "storeid = {$user_id} ".$conditions. " group by customer_mobile",
				'fields'	 => "figure_sn, customer_mobile, lasttime,storeid",
				'order'      => "figure_sn DESC",
				//'limit'	     => $limit,
		'index_key'  => "",
		));
			
		//合并数组
		//$figure_all = array_merge($figure_all_add, $figure_all_in);
		$figure_all_sort = $this->assoc_unique($figure_all, 'customer_mobile');
			
		//处理订单数
		foreach($figure_all_sort as $fkey => $fval) {
				
			$figure_mod_num = $figure_mod->find(array(
					'conditions' => "customer_mobile = {$fval['customer_mobile']} and lw != 0 and storeid = {$user_id}",
					'fields'	 => "figure_sn, storeid, customer_mobile, customer_name, order_num, is_apply, figure_state, lasttime,userid",
					'order'      => "figure_sn desc ",
					'index_key'  => "",
			));
	
			if(count($figure_mod_num)) {
				$figure_all_sort[$fkey] =  $figure_mod_num['0'];
				$figure_all_sort[$fkey]['figure_state'] =  1;
			} else {
				$figure_mod_num = $figure_mod->find(array(
						'conditions' => "customer_mobile = {$fval['customer_mobile']} and storeid = {$user_id}",
						'fields'	 => "figure_sn, storeid, customer_mobile, customer_name,userid, order_num, is_apply, figure_state, lasttime,userid",
						'order'      => "figure_sn desc ",
						'index_key'  => "",
				));
					
				$figure_all_sort[$fkey] =  $figure_mod_num['0'];
			}
			$all_order_num = $order_mod->get(array(
					'conditions' => "user_name = {$fval['customer_mobile']}  and status != 0 and extension = 'news' ",
					'fields'	 => "count(order_id) as count",
			));
	
			foreach($figure_mod_num as $key=>$val){
					
				if($figure_all_sort[$fkey]['userid']){
					$figure_all_sort[$fkey]['type']=1;
				}else{
					$figure_all_sort[$fkey]['type']=0;
				}
				$remarks=$remark_mod->get(array(
						'conditions' =>"store_id='{$user_id}' AND figure_sn='{$val['figure_sn']}'",
				));
				if($remarks){
					$figure_all_sort[$fkey]['customer_name']=$remarks['customer_name'];
					$figure_all_sort[$fkey]['customer_mobile']=$remarks['customer_mobile'];
				}
				if(!empty($val['userid'])){
					$avatar=$mem_mod->get(array(
							'conditions' =>"user_id='{$val['userid']}'",
							'fields'=>"avatar",
					));
					if(!empty($avatar['avatar'])){
						$figure_all_sort[$fkey]['avatar']=LOCALHOST1.'/'.$avatar['avatar'];
					}else{
						$figure_all_sort[$fkey]['avatar']='';
					}
	
				}else{
					$figure_all_sort[$fkey]['avatar']='';
				}
			}
	
			$figure_all_sort[$fkey]['order_num'] = !empty($all_order_num['count']) ? $all_order_num['count'] : 0;
	
			$figure_all_sort[$fkey]['figure_type'] = 'add';
			if(in_array($fval['customer_mobile'], $invitee_arr)) {//说明是添加顾客，并且已有量体信息
				$figure_all_sort[$fkey]['figure_type'] = 'invite';
			}
		} */
			
			
		/*
		 //处理订单数
		foreach($figure_all_sort as $skey => $sval) {
		$all_order_num = $order_mod->get(array(
				'conditions' => "user_name = {$sval['customer_mobile']} ",
				'fields'	 => "count(order_id) as count",
		));
	
		$figure_all_sort[$skey]['order_num'] = !empty($all_order_num['count']) ? $all_order_num['count'] : 0;
		}*/
	
	/* 	$return = array(
				'statusCode' => 1,
				'result'     => array(
						'data'  => $figure_all_sort,
				)
		);
		return $json->encode($return);
	}  */
	/**
	 * 大数据平台-门店-门店列表接口-邵子珍
	 *
	 *///http://local.soaapi.cotte.com/soap/shao.php?act=serve_c2m
	 function serve_c2m($data)
	{
		global $json;
		$serve_id     = isset($data->serve_id) ? $data->serve_id: '';
		$serve = m('serve');
		$m_mod = m('member');
		
		if(!$serve_id) {
			$servec=$serve->get(array(
					'conditions' =>"state=1",
			));
		}else{
			$servec=$serve->get(array(
					'conditions' =>"idserve='{$serve_id}'",
			));
		}
		
		if (!$servec) {
			$this->errorCode = 101;
			$this->msg = '门店不存在';
			return $this->eresult();
		}
		
		$return = array(
				'statusCode' => 1,
				'result'     => array(
						'data'  => $servec,
				)
		);
		return $json->encode($return);
		
	
	} 
		
	
	}
	