<?php

/*分类控制器*/
class CustomerApp extends MemberbaseApp
{
    /* 商品分类 */
    function index()
    { 
    	$store=$this->visitor->get();
    	$member=m("member");
        $store_id=$store['user_id'];
    	$page = $this->_get_page(15);
    	$order=m("order");
    	$member_figure_mod = m('customer_figure');
    	$figure_list =$member_figure_mod->find(array(
    			'fields'			=> "figure_sn,userid,storeid,customer_name,customer_mobile,order_num,figure_state,remark,remark_phone",
    			'conditions' 		=> "storeid='{$store_id}' AND type_cus in(1,2,3)",
    			'limit'				=> $page['limit'],
    			'order'			    => 'lasttime DESC',
    			'index_key'			=> '',
    	));
   
    	foreach($figure_list as $key=>$val)
    	{
    		$as=$member_figure_mod->get(array(
    				'conditions' =>"customer_mobile='{$val['customer_mobile']}' AND figure_state=1",
    				'fields' =>"figure_sn",
    		));
    		if($val['userid']){
    			$avatars=$member->get(array(
    					'conditions' =>"user_id='{$val['userid']}'",
    					'fields'   =>"avatar",
    			));
    			if($avatars['avatar']){
    				$figure_list[$key]['avatar']=LOCALHOST1.$avatars['avatar'];
    			}else{
    				$figure_list[$key]['avatar']=LOCALHOST1."/upload/../avatar/noavatar_big.gif";
    			}
    			
    		}else{
    			$figure_list[$key]['avatar']=LOCALHOST1."/upload/../avatar/noavatar_big.gif";
    			
    		}
    		
    		
    		if($as)
    		{
    			$figure_list[$key]['figure_state']=1;
    			$figure_list[$key]['figure_sn']=$as['figure_sn'];
    		}
    		if($val['remark'])
    		{
    			$figure_list[$key]['customer_name']=$val['remark'];
    			$figure_list[$key]['customer_mobile']=$val['remark_phone'];
    		}
    		$orders=$order->get(array(
    				'conditions' =>"user_id='{$figure_list['userid']}' AND (status=30 or status=40 or status=60)",
    				'fields' =>"count(order_id) as count",
    		));
    		$figure_list[$key]['order_num']=$orders['count'] ? $orders['count'] : 0;
    	}
    	
    	
    	
    	$assess=$member_figure_mod->get(array(
    			'conditions' =>"storeid='{$store_id}' AND type_cus in(1,2,3)",
    			'fields' =>"count(*) as NumberOfAsses",
    	));
    	
    	$page['item_count']=$assess['NumberOfAsses'];
    	$this->_format_page($page);
    	$this->assign('app', "customer");
    	$this->assign('page_info', $page);
    	$this->assign('figure',$figure_list);
        $this->display('customer.index.html');
    }

    /**
     *    获取分页信息
     *
     *    @author    Garbin
     *    @return    array
     */
    function _get_page($page_per = 15)
    {
    	$page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']);
    	$start = ($page -1) * $page_per;
    
    	return array('limit' => "{$start},{$page_per}", 'curr_page' => $page, 'pageper' => $page_per);
    }

    /**
     * 格式化分页信息
     * @param   array   $page
     * @param   int     $num    显示几页的链接
     */
    function _format_page(&$page, $num = 15)
    {
    	$page['page_count'] = ceil($page['item_count'] / $page['pageper']);
    	
    	$mid = ceil($num / 2) - 1;
    	if ($page['page_count'] <= $num)
    	{
    		$from = 1;
    		$to   = $page['page_count'];
    	}
    	else
    	{
    		$from = $page['curr_page'] <= $mid ? 1 : $page['curr_page'] - $mid + 1;
    		$to   = $from + $num - 1;
    		$to > $page['page_count'] && $to = $page['page_count'];
    	}
    	
    	/*
    	 if (preg_match('/[&|\?]?page=\w+/i', $_SERVER['REQUEST_URI']) > 0)
    	 {
    	$url_format = preg_replace('/[&|\?]?page=\w+/i', '', $_SERVER['REQUEST_URI']);
    	}
    	else
    	{
    	$url_format = $_SERVER['REQUEST_URI'];
    	}
    	*/
    
    	/* 生成app=goods&act=view之类的URL */
    	if (preg_match('/[&|\?]?page=\w+/i', $_SERVER['QUERY_STRING']) > 0)
    	{
    		
    		$url_format = preg_replace('/[&|\?]?page=\w+/i', '', $_SERVER['QUERY_STRING']);
    		
    	}
    	else
    	{
    		$url_format = $_SERVER['QUERY_STRING'];
    		
    	}
   
    	$page['page_links'] = array();
    	$page['first_link'] = ''; // 首页链接
    	$page['first_suspen'] = ''; // 首页省略号
    	$page['last_link'] = ''; // 尾页链接
    	$page['last_suspen'] = ''; // 尾页省略号
    	for ($i = $from; $i <= $to; $i++)
    	{
    	$page['page_links'][$i] = "customer.html?{$url_format}&page={$i}";
    	}
    	if (($page['curr_page'] - $from) < ($page['curr_page'] -1) && $page['page_count'] > $num)
    	{
    	$page['first_link'] = "customer.html?{$url_format}&page=1";
    			if (($page['curr_page'] -1) - ($page['curr_page'] - $from) != 1)
    			{
    	$page['first_suspen'] = '..';
    	}
    	}
    	if (($to - $page['curr_page']) < ($page['page_count'] - $page['curr_page']) && $page['page_count'] > $num)
    	{
    	$page['last_link'] = "customer.html?{$url_format}&page=" . $page['page_count'];
    			if (($page['page_count'] - $page['curr_page']) - ($to - $page['curr_page']) != 1)
    			{
    			$page['last_suspen'] = '..';
    	}
    	}
    	
    	$page['prev_link'] = $page['curr_page'] > $from ? "customer.html?{$url_format}&page=" . ($page['curr_page'] - 1): "";
    	$page['next_link'] = $page['curr_page'] < $to ? "customer.html?{$url_format}&page=" . ($page['curr_page'] + 1) : "";
    	}
    /**
     * 添加顾客
     */
  function add()
    {
    	$store=$this->visitor->get();
    	$customer_name=$_POST['customer_name'];
    	$customer_mobile=$_POST['customer_mobile'];
       $store_id=$store['user_id'];
    	$customer_mod    =&m('customer_figure');
    	$mod          =&m('member');
    	$member_invite= m('memberinvite');
    	$type_cus=1;
    	if (!$customer_mod->unique($customer_mobile,$storeid,$type_cus))
    	{
    		$this->json_error("手机号已存在!");
    		return;
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
    	}
    	$data=array(
    			'customer_name' => $customer_name,
    			'customer_mobile' =>$customer_mobile,
    			'storeid' =>$store_id,
    			'firsttime' => time(),
    			'type_cus' =>3,
    			'firsttime' =>time(),
    			'lasttime' =>time(),
    	);
    	$cusmod=$customer_mod->add($data);
    	if($cusmod===false){
    		$this->json_error("添加顾客失败！");
    		return ;
    	}
    	$this->json_result($cusmod);
    	return;
   
  	 
    }
    /**
     * 查看顾客信息
     */
    function getlist()
    {
    	$figure_sn=$_POST['figure_sn'];
    	$customer_mod    =&m('customer_figure');
    	$mod          =&m('member');
    	$cusmod=$customer_mod->get(array(
    			'conditions' =>"figure_sn='{$figure_sn}'",
    			'fields' => "customer_name,customer_mobile",
    	));
    	
    	$this->json_result($cusmod);
    	return;
    
    
    }
    function drop()
    {
    	$figure_sn=$_POST['figure_sn'];
    	$customer_mod    =&m('customer_figure');
    	$cusmod=$customer_mod->drop($figure_sn);
    	if($cusmod===false){
    		$this->json_error("修改顾客失败！");
    		return ;
    	}
    	$this->json_result($cusmod);
    	return;
    }
    /**
     * 修改顾客
     */
    function edit()
    {
    	$customer_name=$_POST['customer_name'];
    	$customer_mobile=$_POST['customer_mobile'];
        $figure_sn=$_POST['figure_sn'];
    	$customer_mod    =&m('customer_figure');
    	$mod          =&m('member');
    	 
    
    	if (!$customer_mod->unique($customer_mobile,$storeid,$figure_sn))
    	{
    		$this->json_error("手机号已存在!");
    		return;
    	}
    
    	$data=array(
    			'customer_name' => $customer_name,
    			'customer_mobile' =>$customer_mobile,
    			'lasttime' => time(),
    	);
    	$cusmod=$customer_mod->edit($figure_sn,$data);
    	if($cusmod===false){
    		$this->json_error("修改顾客失败！");
    		return ;
    	}
    	$this->json_result($cusmod);
    	return;
    	 
    
    }
    
    
    //查看已有量体数据的顾客信息
      function check()
      {
      	$user_info=$this->visitor->get();
		$customer_mobile   = $_GET['customer_mobile'];
		$customer_name= $_GET['customer_name'];
		$member_figure_mod = m('customer_figure');
		$member=m("member");
		$order=m("order");
		$serve=m("serve");
		if(!$customer_mobile){
			$this->show_warning('没有要查看的客户手机号');
			return;
		}
	
		$figure = $member_figure_mod->find(array(
				'conditions' =>"customer_mobile='{$customer_mobile}' AND figure_state=1",
				//  group by customer_mobile
				//'fields'=>"figure_sn,liangti_id,id_serve,lasttime,liangti_name,service_mode",
				'order' =>"lasttime DESC, figure_sn ASC",
				'index_key' =>"liangti_id",
		));
		if($figure){
			
			foreach($figure as $key=>$val)
			{
				if($val['id_serve']){
					$serves=$serve->get(array(
							'conditions' =>"idserve='{$val['id_serve']}'",
							'fields' =>"linkman,serve_name,userid",
					));
				}
				if($val['liangti_id'] !=0){
					$figures[$val['liangti_id']]=$val;
				}else{
					if($serves['userid']){
					$figures[$serves['userid']]=$val;
					}
				}
				
			
				$figures[$key]['figure_info'] = $this->_figure_positions($val);
					
				
				
				if(!empty($val['liangti_name'])){
					$figures[$key]['name']=$val['liangti_name'];
				}else{
					if(!empty($val['liangti_id']))
					{
						$mems=$mem_mod->get(array(
								'conditions'=>"user_id='{$val['liangti_id']}'",
								'fields'=>"real_name",
						));
						if(!$mems['real_name']){
							if($val['service_mode']== 4){
								$figures[$key]['name']=$val['liangti_name'];
							}else{
								$figures[$key]['name']='';
							}
								
						}else{
							$figures[$key]['name']=$mems['real_name'];
								
						}
					}else{
						if(!$serves['linkman']){
							$figures[$key]['name']='';
						}else{
							$figures[$key]['name']=$serves['linkman'];
						}
					}
				}
				
			
				
				if(!$serves['serve_name']){
						
					if($val['service_mode']== 4){
							
						$figures[$key]['serve_name']='后台录入';
							
					}else{
						$figures[$key]['serve_name']='';
					}
					
				}else{
					$figures[$key]['serve_name']=$serves['serve_name'];
				}
				
				if(!$val['lasttime']){
					$figures[$key]['lasttime']=0;
				}else{
					$figures[$key]['lasttime']=date('Y-m-d H:i:s',$val['lasttime']);
					
				}
				
		/* $style   = array();//风格
		$feature = array();//特体 */
		//获得风格特体
			//处理获得体型和风格
			$_get_body_type = $this->_get_body_type(0);
			
			//处理风格数据
			
			foreach($_get_body_type['style'] as $style_key => $style_val) {
				$nm = $style_val['info']['nm'];
				
				foreach($style_val['list'] as $lkey => $lval ) {
					
				 if( $lkey == $figures[$key][$nm]) {
				 	
				$figures[$key]['style'][] = array(
						'name' => $lval['clothName'],
						'val'  => $lval['name'],
				);
				
				}
				}
				
			}
			//处理特体数据
	
			foreach($_get_body_type['feature'] as $feature_key => $feature_val) {
				
				$nm = $feature_val['info']['nm'];
				//print_r($nm);
				foreach($feature_val['list'] as $fkey => $fval ) {
					
					if( $fkey ==  $figures[$key][$nm]) {
						$figures[$key]['feature'][] = array(
								'name' => $fval['cateName'],
								'val'  => $fval['name'],
						);
					}
				}
			}
			
		}
			
		}
		$this->assign('app', "customer");
		$this->assign('customer_name',$customer_name);
		$this->assign('figure',$figures);
		//$this->assign('figure_info',$figure_info);
		//$this->assign('style',$style);
		//$this->assign('feature',$feature);
		$this->display("customer.info.html");
}

/**
 * 处理量体数据返回像   22项量体信息
 */
 function _figure_positions($figure)
{
	$_mod_member_figure = m('customer_figure');

	$_figure = $_mod_member_figure->_positions();
	
	$unit = $figure['unit'];

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
			array('name' => $_figure['zxc']['zname'], 'val' => !empty($figure['zxc']) ? $figure['zxc'].$unit: '0'.$unit),
			array('name' => $_figure['yxc']['zname'], 'val' => !empty($figure['yxc']) ? $figure['yxc'].$unit: '0'.$unit),
			array('name' => $_figure['qjk']['zname'], 'val' => !empty($figure['qjk']) ? $figure['qjk'].$unit: '0'.$unit),
			array('name' => $_figure['hyjc']['zname'], 'val' => !empty($figure['hyjc']) ? $figure['hyjc'].$unit: '0'.$unit),
			array('name' => $_figure['hyc']['zname'], 'val' => !empty($figure['hyc']) ? $figure['hyc'].$unit: '0'.$unit),
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
			array('name' => $_figure['dyhyc']['zname'], 'val' => !empty($figure['dyhyc']) ? $figure['dyhyc'].$wunit: '0'.$wunit),
				
	);
	return $figure_info;
}

/**
 * 根据品类id获得对应体形及风格
 */
 function  _get_body_type($clothId){
	$_mod_mtm_bt = &m("mtmbodytype");
	$body_type_tm = $_mod_mtm_bt->find("clothId in(3,2000,3000,4000,6000,11000,95000,98000)");
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



}

?>