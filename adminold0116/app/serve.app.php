<?php


class ServeApp extends BackendApp
{
	var $_serve,$serve_type,$wait_verify_str,$_member;
	var $_figure_liangti;
	var $_figure_om_mod;
	var $_figure_cs_mod;
    var $_figure_cash_mod;
    var $_order_goods_mod;
    function __construct()
    {
        $region_mod = m('region');
        $list1 = $region_mod->get_options(2);
        $this->assign('region1',$list1);
        /* $list2 = $region_mod->get_options(246);
        $this->assign('region2',$list2); */
        
        parent::__construct();
        $this->_serve =& m ('serve');
        $this->_member =& m ('member');
        $this->_figure_liangti =& m ('figure_liangti');
        $this->serve_type = 2;
        
    	
    	parent::__construct();
        $this->_serve =& m ('serve');
        $this->_member =& m ('member');
        $path = ROOT_PATH."/data/config/serve_type.php";
        file_exists($path) && $this->_type = include $path;
        
     
        $this->_figure_liangti =& m ('figure_liangti');
        $this->_figure_om_mod  =& m('figureorderm');
        $this->_figure_cs_mod  =& m('customer_figure');
        $this->_figure_cash_mod = & m('figurecash');
        $this->_order_goods_mod = & m('ordergoods');
        if($_GET['t']=='2')//量体派工
        {
        	$this->serve_type=2;
        }elseif($_GET['t']=='4'){ // ns add 设计师
            $this->serve_type=4;
        }
        if($_GET['wait_verify']=='1')
        {
        	$this->wait_verify_str=' and state=0 ';
        }else {
        	$this->wait_verify_str=' and state=1 ';
        }
        
    }



    function index()
    {
    	if($_GET['field_name'] == 'user_name'){  //dianzhang
    		$conditions = $this->_get_query_conditions(array(
    				array(
    						'field' => $_GET['field_name'],
    						'name'  => 'field_value',
    						'equal' => 'LIKE',
    				),
    		));
    	}elseif ($_GET['field_name'] == 'phone_mob'){  //liangtishi
    		$conditions2 = $this->_get_query_conditions(array(
    				array(
    						'field' => $_GET['field_name'],
    						'name'  => 'field_value',
    						'equal' => 'LIKE',
    				),
    		));    		
    	}
    	//更新排序
        if (isset($_GET['sort']) && !empty($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'idserve';
             $order = 'desc';
            }
        }
        else
        {
            if (isset($_GET['sort']) && empty($_GET['order']))
            {
                $sort  = strtolower(trim($_GET['sort']));
                $order = "";
            }
            else
            {
                $sort  = 'idserve';
                $order = 'desc';
            }
        }
        
        $page = $this->_get_page(30);

        $contis = $conditions ? $conditions . " AND figure_type=1" : ($conditions2 ? $conditions2 ." AND figure_type=2" : ""); //
        $managers=$this->_member->find(array(
        		'conditions'=>"serve_type=2 ".$contis, //figure_type=1
        		'count' => true,
        ));
        foreach($managers as $key=>$val){
        	if($val['figure_type'] == '1' && $conditions1){
        		$mIds[$key] = $key;
        	}elseif( $val['figure_type'] == '2'  && $conditions2){
        		$lIds[$key] = $key;
        	}
        }
        if($mIds){
        	$cons = " AND ".db_create_in($mIds,'manager_id');
        }elseif($lIds){
        	$cons = " AND ".db_create_in($lIds,'liangti_id');
        }
        $liangtis = $this->_figure_liangti->find(array(
        		'conditions' => "1=1  " . $cons,
        		'fields'=>'liangti_id,manager_id',
        		'limit'         => $page['limit'],  //获取当前页的数据
        		//'order' => "manager_id",
        		'count' => true,
        ));
        $liangids = i_array_column($liangtis, 'liangti_id');
        $condition = db_create_in($liangids,'user_id');
        $liangti = $this->_member->find(array(
        		'conditions' =>$condition,
        		'fields'=>'user_name,real_name,state_info,gender,phone_mob',
        		'order' => "user_id DESC",
        		'count' => true,
        ));
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        
        $manager2=$this->_member->find(array(
        		'conditions'=>"serve_type=2 AND figure_type=1", //
        		'count' => true,
        ));
        
      foreach($liangtis as $key=>$val){
      	$liangti[$val['liangti_id']]['manager_name'] = $manager2[$val['manager_id']]['user_name'];
      }
      $model_order =& m('order');
        $this->assign('liangti', $liangti);
        $page['item_count'] = $this->_figure_liangti->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('manager', $manager);
        
        
        $this->assign('sort_options', array(
            'create_date DESC'   => LANG::get('create_date'),
            'idserve DESC' => LANG::get('idserve'),
            'establish_date DESC'     => LANG::get('establish_date'),
        ));
        if($this->serve_type == '4')
        {
        	$this->assign('page_info', $page);
             $this->assign('query_fields', array(
            'serve.linkman' => LANG::get('linkman'),
            'serve.email'     => LANG::get('email'),
            'member.user_name' => LANG::get('user_name'),
            ));
            $this->display('serve_designer.index.html');
        }elseif($this->serve_type == '2'){//量体派工管理页面
        	
             $this->assign('query_fields', array(
            'user_name' =>'门店名称',
            'phone_mob'     =>'量体师手机号',
            ));
            $this->display('serve.liangti.index.html');
        }else{
             $this->assign('query_fields', array(
            'serve.linkman' => LANG::get('linkman'),
            'serve.email'     => LANG::get('email'),
            'serve.company_name' => LANG::get('company_name'),
            'member.user_name' => LANG::get('user_name'),
            ));
            $this->display('serve/serve.index.html');
        } 
    }
    function _suit_conditions($get = array()){
    	//$field = 'order_sn';
    	//array_key_exists($get['field'], self::$search_options) && $field = $get['field'];
    	/* rcmtm订单号 查询用like 如果效率慢可以考虑 find_in_set  */
    	$conditions = $this->_get_query_conditions(array(array(
    			'field' => $field,       //客户姓名，客户手机，收货人姓名，收货人手机进行搜索
    			'equal' => 'LIKE',
    			'name'  => 'search_name',
    	),
    			array(
    					'field' => 'status',
    					'equal' => '=',
    					'type'  => 'numeric',
    			),array(
    					'field' => 'add_time',
    					'name'  => 'add_time_from',
    					'equal' => '>=',
    					'handler'=> 'gmstr2time',
    			),array(
    					'field' => 'add_time',
    					'name'  => 'add_time_to',
    					'equal' => '<=',
    					'handler'   => 'gmstr2time_end',
    			),array(
    					'field' => 'order_amount',
    					'name'  => 'order_amount_from',
    					'equal' => '>=',
    					'type'  => 'numeric',
    			),array(
    					'field' => 'order_amount',
    					'name'  => 'order_amount_to',
    					'equal' => '<=',
    					'type'  => 'numeric',
    			),
    	));
    	/* 量体信息查询 */
    	if($get['has_measure']){
    		$conditions .= " AND has_measure=".substr($get['has_measure'],-1);
    	}
    	return $conditions;
    }
  //量体师管理页面
    function quantity(){
    	$conditionoo = $this->_suit_conditions($_GET);
    	if($_GET['field_name'] == 'serve_name'){  //dianzhang
    		$conditions = $this->_get_query_conditions(array(
    				array(
    						'field' => $_GET['field_name'],
    						'name'  => 'field_value',
    						'equal' => 'LIKE',
    				),
    		));
    	}elseif ($_GET['field_name'] == 'phone_mob'){  //liangtishi
    		$conditions2 = $this->_get_query_conditions(array(
    				array(
    						'field' => $_GET['field_name'],
    						'name'  => 'field_value',
    						'equal' => 'LIKE',
    				),
    		));
    	}elseif ($_GET['field_name'] == 'real_name'){  //liangtishi
    		$conditions2 = $this->_get_query_conditions(array(
    				array(
    						'field' => $_GET['field_name'],
    						'name'  => 'field_value',
    						'equal' => 'LIKE',
    				),
    		));    		
    	}
    	//更新排序
    	if (isset($_GET['sort']) && !empty($_GET['order']))
    	{
    		
    		$sort  = strtolower(trim($_GET['sort']));
    		$order = strtolower(trim($_GET['order']));
    		if (!in_array($order,array('asc','desc')))
    		{
    			$sort  = 'idserve';
    			$order = 'desc';
    		}
    	}
    	else
    	{
    		if (isset($_GET['sort']) && empty($_GET['order']))
    		{
    			$sort  = strtolower(trim($_GET['sort']));
    		
    			$order = "";
    		}
    		else
    		{
    			$sort  = 'user_id';
    			$order = 'desc';
    		}
    	}
    	
    	$page = $this->_get_page(30);
    	
    	$serves=$this->_serve->find(array(
    			'conditions' => "1=1".$conditions,
    			'fields' =>"userid,serve_name",
    			'index_key'=>'userid',
    	));
    	$serve_id = i_array_column($serves, 'userid');
    		$managers=$this->_member->find(array(
    			'conditions'=>"serve_type=2 AND figure_type=2".$conditions2, 
    			//'fields' =>"real",
    			'count' => true,
    	));
    	
    	$mans = i_array_column($managers, 'user_id');
    	if($conditions){
    		$cons = " AND ".db_create_in($serve_id,'manager_id');
    	}elseif($conditions2){
    		$cons = " AND ".db_create_in($mans,'liangti_id');
    	}
    	$liangtis = $this->_figure_liangti->find(array(
    			'conditions' => "1=1  ".$cons ,
    			'fields'=>'liangti_id,manager_id,alone',
    			'limit'         => $page['limit'],  //获取当前页的数据
    			//'order' => "manager_id",
    			'count' => true,
    	));
    	$liangids = i_array_column($liangtis, 'liangti_id');
    	$condition = db_create_in($liangids,'user_id');
    	$liangti = $this->_member->find(array(
    			'conditions' =>"figure_type=2 AND serve_type=2 AND ".$condition ,
    			'join' =>"has_figurel",
    			'fields'=>'user_name,real_name,money,state_info,gender,phone_mob,figure_liangti.manager_id,figure_liangti.alone',
    			'order' => "$sort $order",
    			'count' => true,
    	));
    	$this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
    	
    	$manager2=$this->_serve->find(array(
    			'conditions'=>'1=1',
    			'fields'=>'serve_name,userid',
    			'index_key'=>'userid',
    	));
    	
    
    	foreach($liangtis as $key=>$val){
    	   $liangti[$val['liangti_id']]['alone'] = $val['alone'];
    	   $liangti[$val['liangti_id']]['manager_name'] = $manager2[$val['manager_id']]['serve_name'];
    	}
    	//  已经服务顾客数量 
    	
    	foreach($liangti as $k=>$v)
    	{
    	    $res =  $this->_figure_cs_mod->find(array(
    	        'conditions' =>"liangti_id ='{$v['user_id']}' GROUP BY customer_mobile",
    	        'fields'     =>"*",
    	    ));
    	    $liangti[$k]['num'] = count($res);
    	    
    	    $fees = $this->_figure_cs_mod->find(array(
    	        'conditions' =>"liangti_id ='{$v['user_id']}' AND liangti_state != 3",
    	        'fields'     =>"service_mode",
    	        'index_key'  =>'',
    	    ));
    	    if($fees)
    	    {
    	         foreach ($fees as $kk=>$vv)
    	        {
    	            $fees[$kk]['single_fee'] =$this->_type[$vv['service_mode']]['single'];
    	           
    	        } 
    	        $sum = 0.00;
    	        foreach($fees as $item){
    	            $sum += (double) $item['single_fee'];
    	        }
    	        $liangti[$k]['sum'] = $sum + $liangti[$k]['money'];
    	    }else{
    	        
    	        $liangti[$k]['sum'] = $liangti[$k]['money'];
    	    }
    	    
    	}
    

    	$model_order =& m('order');
        // 已经服务顾客数量
       
    	$this->assign('liangti', $liangti);
    	$page['item_count'] = $this->_figure_liangti->getCount();
    	$this->_format_page($page);
    	$this->assign('page_info', $page);
    	if($_GET){
    		$this->assign('get',$_GET);
    	}
    	$this->assign('manager', $manager);
    	
    	
    	$this->assign('sort_options', array(
    			'create_date DESC'   => LANG::get('create_date'),
    			'idserve DESC' => LANG::get('idserve'),
    			'establish_date DESC'     => LANG::get('establish_date'),
    	));
  
    		 
		$this->assign('query_fields', array(
				'real_name' =>'量体师姓名',
				'phone_mob'     =>'量体师手机号',
				'serve_name' =>'门店名称',
		));
		$this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
				'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
		$this->display('serve.liangti.index.html');
    }
	function drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_user_to_drop');
            return;
        }
        
        $serve = $this->_serve->get_info($id);
        $ids = explode(',', $id);
        if (!$this->_serve->drop($ids))
        {
            $this->show_warning($this->_serve->get_error());

            return;
        }
        
        
        $mod = m('member');
        
        $mod->drop("user_id = {$serve['userid']}");
        
        //=====  删除门店更新region表信息  =====
        $region_mod = m('region');
        $region_id = $serve['region_id'];
        if ($region_id) 
        {
            $serve_region = $this->_serve->get("region_id = $region_id");
            if (!$serve_region) //=====  如果没有其他门店在这个城市下面 那么这个城市的is_serve要置为0  ===== 
            {
                $region_mod->edit($region_id,array('is_serve' => 0));
                
                $region_info = $region_mod->get_info($region_id);
                $p_id = $region_info['parent_id'];
                $region_list = $region_mod->get("parent_id = $p_id AND is_serve = 1");
                if (!$region_list) 
                {
                    $region_mod->edit($p_id,array('is_serve' => 0));
                }
            }
        }

        $this->show_message('drop_ok');
    }
    
    function smg(){
    	
//    	$reject_reason='sss';
//		$content = get_msg('toseller_serve_2_refused_notify', array('reason' => $reject_reason));
//		
//        //var_dump($content);exit;
//		$ms =ms();
//        $ms->pm->send(MSG_SYSTEM, $serve['userid'], '', $content);

        
    }
    
    
    function indexManager()
    {
         
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => $_GET['field_name'],
                'name'  => 'field_value',
                'equal' => 'LIKE',
            ),
        ));
        // var_dump($conditions);
        //更新排序
        if (isset($_GET['sort']) && !empty($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
                $sort  = 'idserve';
                $order = 'desc';
            }
        }
        else
        {
            if (isset($_GET['sort']) && empty($_GET['order']))
            {
                $sort  = strtolower(trim($_GET['sort']));
                $order = "";
            }
            else
            {
                $sort  = 'idserve';
                $order = 'desc';
            }
        }
        $page = $this->_get_page(30);
         $serves = $this->_serve->find(array(
            'conditions' => "1=1".$conditions,
            'join' => 'has_member',
            'limit' => $page['limit'],
            'order' => "$sort $order",
         	'fields'=>"serve.region_name,serve.idserve,serve.userid,serve.linkman,serve.mobile,serve.serve_name,serve.serve_address,serve.serve_type,serve.store_mobile,serve.virtual,serve.storetype,serve.create_date",
            'count' => true,
        )); 
        $this->assign('liangti', $liangti);
        $this->assign('serves', $serves);
        $page['item_count'] = $this->_serve->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
    
        $this->assign('query_fields', array(
        	 'serve.serve_name' => '门店',
            'serve.linkman' => LANG::get('linkman'),
            //'serve.email'     => LANG::get('email'),
            'serve.mobile' => '联系电话',
           
        ));
    
    
       
        $this->display('serve/serve.index.html');
    
    }
    
    function edit()
    {
    
        $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
        $serve = $this->_serve->get_info($id);
        if (!IS_POST)
        {
            /* 是否存在 */
            if (!$serve)
            {
                $this->show_warning('user_empty');
                return;
            }
            $region_mod = m('region');
    
            $region_id = $serve['region_id'];
            if ($region_id)
            {
                $region_info = $region_mod->get($region_id);
                $this->assign('p_region_id',$region_info['parent_id']);
                $list2 = $region_mod->get_options($region_info['parent_id']);
                //          dump($list2);
                $this->assign('region2',$list2);
            }
            
            $m_info = $this->_member->get_info($serve['userid']);
            $serve['phone_mob'] = $m_info['phone_mob'];
            $this->assign('serve', $serve);
    
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
    
            if($serve['state'])
            {
                 
                $this->assign('state', array(
                    $serve['state']   => LANG::get('state_'.$serve['state']),
                ));
            }else{
                $this->assign('state', array(
                    '0'   => '未审核',
                    '1' => '正常',
                    '2'     => '注销',
                ));
            }
            $this->display('serve/serve.form.html');
        }
        else
        {
            $mode = m('member');
            $region_mod = m('region');
            //ns up 先把验证去掉。方便测试。因为很多目前不存在
            if($_POST['mobile']){
                if(!preg_match("/1[34587]{1}\d{9}$/",$_POST['mobile']))
                {
                    $this->show_warning('登录账号必须是手机号');
                    return;
                }
                
                
                $_data_m['user_name'] = $_POST['mobile'];
                $_data_m['phone_tel'] = $_POST['mobile'];
                $_data_m['phone_mob'] = $_POST['mobile'];
                $_data_m['real_name'] = $_POST['linkman'];
                
                
                if ($_POST['password']) 
                {
                    $_data_m['password'] = md5($_POST['password']);
                }
                if ($_POST['storetype']==2 && !$mode->get("(user_name = {$_POST['mobile']} OR phone_mob = {$_POST['mobile']}) AND serve_type=1 AND member_lv_id=9")) 
                {
                	$this->show_warning('此店长账号不是VIP合伙人');
                	return ;
                }
                
                $mode->edit($serve['userid'],$_data_m);
            }

            $virtual=isset($_POST['virtual']) ? $_POST['virtual'] : 0;
            $data = array(
                'serve_name' => $_POST['serve_name'],
                'business_time' => $_POST['business_time'],
                'serve_address' => $_POST['serve_address'],
            		'storetype'=>$_POST['storetype'],
                //'company_name' => $_POST['company_name'],
                //'email'    => $_POST['email'],
                 'linkman'     => $_POST['linkman'],
            	 'post_code'   => $_POST['post_code'],
                //'enterprise_url'    => $_POST['enterprise_url'],
                //'company_name' => $_POST['company_name'],
            	'store_mobile' => $_POST['login_name'],
            		'shop_type'     => 1,
                'mobile'    => $_POST['mobile'],
                //'post'    => $_POST['post'],
                //'company_synopsis' => $_POST['company_synopsis'],
                'region_id' => $_POST['region_id'],
            	'card_number'=> $_POST['card_number'],
            	'virtual'=> $virtual,
            );
            
            //=====  修改region表的is_serve字段  =====
            $region_name = "";
            $region_id = $_POST['region_id'];
            $region_info = $region_mod->get_parents($_POST['region_id']);
            if ($region_info)
            {
                $region_ids = implode(",", $region_info);
                //             echo $region_ids;exit;
                $region_list = $region_mod->find(array(
                    'conditions' => "region_id IN ($region_ids) "
                ))  ;
                if ($region_list)
                {
                    foreach ($region_list as $key => $value)
                    {
                        $region_name .= $value['region_name'] ." ";
                    }
                }
            }
            $data['region_name'] = trim($region_name);
            /* 修改本地数据 */
            $this->_serve->edit($id, $data);
            
            
            //=====  修改region表的is_serve字段  =====
            $region_id = $_POST['region_id'];
            if ($region_id != $serve['region_id']) 
            {
                
                $old_region_id = $serve['region_id'];
                $serve_region = $this->_serve->get("region_id = $old_region_id");
                if (!$serve_region) //=====  如果没有其他门店在这个城市下面 那么这个城市的is_serve要置为0  =====
                {
                    $region_mod->edit($old_region_id,array('is_serve' => 0));
                    $region_info = $region_mod->get_info($old_region_id);
                    //=====  城市父id也要置为0  =====
                    $p_id = $region_info['parent_id'];
                    $region_list = $region_mod->get("parent_id = $p_id AND is_serve = 1");
                    if (!$region_list)
                    {
                        $region_mod->edit($p_id,array('is_serve' => 0));
                    }
                }
                
                $region_info = $region_mod->get($region_id);
                $p_id = $region_info['parent_id'];
                $region_mod->edit($region_id,array('is_serve'=>1));
                if ($p_id) 
                {
                    $region_mod->edit($p_id,array('is_serve'=>1));
                }
                
            } 
           
    
            $this->show_message('修改成功',
                'back_list',    'index.php?app=serve&act=indexManager',
                '再次编辑',   'index.php?app=serve&amp;act=edit&amp;id=' . $id.'&t='.$_GET['t']
            );
        }
    }
    
    
    
    
    function add()
    {
         
        if (!IS_POST)
        {
            $region_mod = m('region');
             $list2 = $region_mod->get_options(246);
             $this->assign('region2',$list2); 
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('state', array(
                '0'   => LANG::get('state_0'),
                '1' => LANG::get('state_1'),
                '2'     => LANG::get('state_2'),
            ));
            $this->assign('acc',LANG::get('add'));
            //         	$serve['rc_code']=getRandOnlyId();
            $serve=$_GET;
            $serve['store_mobile']=$_GET['login_name'];
            $this->assign('serve',$serve);
            $this->display('serve/serve.form.html');
        }
        else
        {
    
         //   $email     = trim($_POST['email']);
    
//            if (!is_email($email))
//            {
//                $this->show_warning('email_error');
//    
//                return;
//            }
        /*    if(!preg_match("/1[34587]{1}\d{9}$/",$_POST['mobile']))
            {
                 $this->show_warning('登录账号必须是手机号');
                 return;
            }*/
            
            $password = '123456';
            if ($_POST['password']) 
            {
                $password = $_POST['password'];
            }
           
            $mode = m('member');
            $region_mod = m('region');
            $_data_m['user_name'] = $_POST['mobile'];
          //  $_data_m['email'] = $_POST['email'];
            $_data_m['phone_mob'] = $_POST['mobile'];
            $_data_m['phone_tel'] = $_POST['mobile'];
            $_data_m['serve_type'] = 2;
            $_data_m['figure_type'] = 1;
            $_data_m['password'] = md5($password);
            $_data_m['real_name'] = $_POST['linkman'];
            if($_POST['mobile'] && $_POST['storetype']==2){
            	
	            if ($mode->get("(user_name = {$_POST['mobile']} OR phone_mob = {$_POST['mobile']}) AND serve_type = 1 AND member_lv_id=9"))
	            {
	                
	            }else{
	            	
	            	$pst='';
	            	foreach($_POST as $key=>$val){
	            		$pst .='&'.$key.'='.$val;
	            	}
	            	$this->show_warning('该用户不是VIP合伙人，不能设为自营门店！',
	            			'返回',    'index.php?app=serve&act=add'.$pst
	            	);
	            	return ;
	            }
            
            	
            }
            $user_id = $mode->add($_data_m);
            $virtual=isset($_POST['virtual']) ? $_POST['virtual'] : 0;
            $data = array(
                'serve_name' => $_POST['serve_name'],
                'business_time' => $_POST['business_time'],
                'serve_address' => $_POST['serve_address'],
                //'company_name' => $_POST['company_name'],
                //'email'    => $_POST['email'],
                'storetype' => $_POST['storetype'],
            	'store_mobile' => $_POST['login_name'],
                 'linkman'     => $_POST['linkman'],
            	 'post_code'   =>$_POST['post_code'],
            	 'shop_type'     =>1,//承接业务
               // 'enterprise_url'    => $_POST['enterprise_url'],
                'mobile'    => $_POST['mobile'],
               // 'post'    => $_POST['post'],
               // 'company_synopsis' => $_POST['company_synopsis'],
                'region_id' => $_POST['region_id'],
                'state'    => 1,
                'userid' => $user_id,
            	 'card_number' => $_POST['card_number'],
            	'virtual' => $virtual,
            );
            //=====  修改region表的is_serve字段  =====
            $region_name = "";
            $region_id = $_POST['region_id'];
            $region_info = $region_mod->get_parents($_POST['region_id']);
            if ($region_info) 
            {
                $region_ids = implode(",", $region_info);
//             echo $region_ids;exit;
                $region_list = $region_mod->find(array(
                    'conditions' => "region_id IN ($region_ids) "
                ))  ;
                if ($region_list)
                {
                    foreach ($region_list as $key => $value)
                    {
                        $region_name .= $value['region_name'] ." ";
                    }
                }
            }
            $data['region_name'] = trim($region_name);
            
            
            if ($region_id) 
            {
                $region_info = $region_mod->get($region_id);
                $p_id = $region_info['parent_id'];
                $region_mod->edit($region_id,array('is_serve'=>1));
                if ($p_id) 
                {
                    $region_mod->edit($p_id,array('is_serve'=>1));
                }
                
            } 
            $idserve=$this->_serve->add($data);
            if (!$idserve)
            {
                $this->show_warning('serve表添加失败');
                return;
            }
    
    
            $this->show_message('添加成功',
                'back_list',    'index.php?app=serve&act=indexManager',
                'continue_add', 'index.php?app=serve&amp;act=add&t='.$_GET['t']
            );
        }
    }
    
    /*检查量体师名称的唯一性*/
    function  check_mob()
    {
    	$phone_mob = empty($_GET['mobile']) ? null : trim($_GET['mobile']);
    	if (!$phone_mob)
    	{
    		return;
    	}
    	
    	
    	if(!preg_match("/^1[34578][0-9]{9}$/",$phone_mob))
    	{
    		echo 0;
    		//$this->show_warning('登录账号必须是手机号');
    		return;
    	}
    	if (!$this->_member->uniqueuser($phone_mob))
    	{
    		echo 0;
    	}else{
    		echo 1;
    	}
    	
    }
    
    /*检查的唯一性*/
    function  check_card_number()
    {
    	$card_number = empty($_GET['card_number']) ? null : trim($_GET['card_number']);
    	$id = $_GET['id'];
    	
    	if (!$card_number)
    	{
    		echo 0;
    	}
    	
    	$conditions = "card_number ='$card_number'";
    	if($id)
    	{
    		
    		$conditions .= "AND idserve !=$id";
    	}
//   echo $conditions;exit;  	
    	if($this->_serve->get(array('conditions' => $conditions)))
    	{
    		echo 0;	
    	}else
    	{
    		echo 1;
    	}
    	
    	
    }
    
     /*检查的唯一性*/
    function  check_lt_card()
    {
    	$card_number = empty($_GET['card_number']) ? null : trim($_GET['card_number']);
    	$id = $_GET['id'];
    	
    	if (!$card_number)
    	{
    		echo 0;
    	}
    	
    	$conditions = "card_number ='$card_number'";
    	if($id)
    	{
    		
    		$conditions .= "AND id !=$id";
    	}
    	
    	if($this->_figure_liangti->get($conditions))
    	{
    		echo 0;	
    	}else
    	{
    		echo 1;
    	}
    	
    	
    }
    //检查
    function  check_lt()
    {
    	$ltname = empty($_GET['ltname']) ? null : trim($_GET['ltname']);
    	 
    	if (!$ltname)
    	{
    		echo 0;
    	}
    	if(mb_strlen($ltname,'utf-8')>10){
    		echo 0;
    	}else
    	{
    		echo 1;
    	}
    	 
    }
    function check_mobile()
    {
    	$members=m("member");
    	$mobile= $_GET['mobile'];
    	$user_id=$_GET['user_id'];
    	if (!$mobile)
    	{
    		echo 0;
    	}
    	$cons="(user_name='{$mobile}' or phone_mob='{$mobile}') AND serve_type =2";
    	if($user_id)
    	{
    		$cons.=" AND user_id != '{$user_id}'";
    	}
    	if ($members->get($cons))
    	{
    		echo 0;
    	}else{
    		echo 1;
    	}
    	
    	
    }
    function check_serve_name()
    {
        $serve_name = $_GET['serve_name'];
        $id = $_GET['id'];
        if (!$serve_name) 
        {
            echo 0;
        }
        
        $conditions = "serve_name = '$serve_name' ";
        if ($id) 
        {
            $conditions .= " AND idserve != $id";
        }
        
        
        
        
        if ($this->_serve->get($conditions)) 
        {
            echo 0;
        }
        else 
        {
            echo 1;
        }
        
    }
    function login_name()
    {
    	$serve_name = $_GET['login_name'];
    	$id = $_GET['id'];
    	if (!$serve_name)
    	{
    		echo 0;
    	}
    
    	$conditions = "mobile = '$serve_name' ";
    	if ($id)
    	{
    		$conditions .= " AND idserve != $id";
    	}
    
    	if ($this->_serve->get($conditions))
    	{
    		echo 0;
    	}
    	else
    	{
    		echo 1;
    	}
    
    }
    
    function check_login_name()
    {
        $member_mod = m('member');
        $serve_name = $_GET['login_name'];
        $id = $_GET['id'];
        if (!$serve_name)
        {
            echo 0;
        }
        
        $conditions = "(phone_mob = '$serve_name' OR user_name = '$serve_name' ) AND serve_type=2 ";
        if ($id)
        {
            $conditions .= " AND user_id != $id";
        }
    
        if ($member_mod->get($conditions))
        {
            echo 0;
        }
        else
        {
            echo 1;
        }
    
    }
    
    
    
    /*检查量体师身份证的唯一性*/
    function  check_ident()
    {
    	$card = empty($_GET['ident_card']) ? null : trim($_GET['ident_card']);
    	//===== 正则验证身份证 =====
    	$preg18 = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/";
    	$preg15 = "/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/";
    	if (!preg_match($preg18, $card) && !preg_match($preg15, $card))
    	{
    		echo 0;
    		return;
    	}else{
    		echo 1;
    	}
    	 
    }
    //量体师添加 by shao
    function addlt()
    {
    	$managers=$this->_serve->find(array(
    			'conditions'=>'1=1',
    			'fields'=>'serve_name,userid',
    	));
    	
    	foreach($managers as $key=>$val){
    		$manager[$val['userid']]=$val['serve_name'];
    	}
    	if (!IS_POST)
    	{
    		
    		

    		/* 导入jQuery的表单验证插件 */
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->assign('state', array(
    				'0'   => '未审核',
    				'1' => '正常',
    				'2'     => '注销',
    		));
    		$this->assign('acc',LANG::get('add'));
    		$serve['rc_code']=getRandOnlyId();
    		//$this->assign('serve',$serve);
    		$this->assign('manager',$manager);
    		$this->display('serve.liangti.form.html');
    	}
    	else
    	{
    		$user_name = trim($_POST['mobile']);
    		$pass=md5($_POST['password1']);
    		$data = array(
    		'real_name'	=>	trim($_POST['ltname']),//量体师名
    		'gender'	=>	$_POST['gender'],//性别
    		'phone_mob'	=>	$_POST['mobile'],//绑定电话
    		'user_name'	=>	$user_name,//登陆账号
    		'password'	=>	$pass,//密码
    		
    		'state_info'=>	$_POST['state'],//审核状态
    	    //'avatar'	=>	$data_dir,//头像
    	    'user_token'=>  md5($user_name.$pass),
    	    'figure_type' => 2,// 1店长2量体师
            'serve_type'=>2,

    		
    		);
    	    if (!$this->_member->uniqueuser($user_name))
    				{
    					$this->show_message('手机号已存在!');
    					return;
    				}
    		$idslt=$this->_member->add($data);
    		$datalt = array(
    		'manager_id'	=>	$_POST['manager'],//量体师所属店长id
    		
    		'ident_card'	=>	trim($_POST['id_card']),//身份证
    		'liangti_id'    =>  $idslt,//量体师id
            'card_number'   =>  trim($_POST['card_number']),
    		'alone'=> $_POST['alone'],
    		);
    		$lt=$this->_figure_liangti->add($datalt);
    		
    		if (!$idslt)
    		{
    			$this->show_warning($this->_member->get_error());
    			return;
    		}
    		if (!$lt)
    		{
    			$this->show_warning($this->_figure_liangti->get_error());
    			return;
    		}
    		$this->show_message('添加成功',
    				'back_list',    'index.php?app=serve&act=quantity',
    				'continue_add', 'index.php?app=serve&amp;act=add&t=2'
    		);
    	}
    }

    function editlt()
    {
    
    	$id = isset($_GET['id']) ?intval($_GET['id']) : '';
    		if (!$id)
    		{
    			$this->show_warning('没有对应的用户id');
    			return;
    		}	
    	
    	$managers=$this->_serve->find(array(
    			'conditions'=>'1=1',
    			'fields'=>'serve_name,userid',
    	));
    	 
    	foreach($managers as $key=>$val){
    		$manager[$val['userid']]=$val['serve_name'];
    	}
    	$datalt=$this->_member->get(array(
    			'conditions' => "user_id='{$id}'",
    	));
    	$liangt=array();
    	$liangt=$this->_figure_liangti->get(array(
    			'conditions' => "liangti_id='{$id}'",
    	));
    
    	if(empty($liangt)){
    		$this->show_message("错误");
    		return;
    	}
    	if (!IS_POST)
    	{
    
    		/* 是否存在 */
    		$serve = $this->_member->get_info($id);
    		if (!$serve)
    		{
    			$this->show_warning('user_empty');
    			return;
    		}
    			
    		$memberlv_mod=m('memberlv');
    		$memberlv_data=$memberlv_mod->get($serve['brokerage_level']);
    		$serve['brokerage_level']=$memberlv_data['name'];
    		$this->assign('serve', $serve);
    		$this->assign('liangt',$liangt);
    		$this->assign('manager',$manager);
    		$this->assign('datalt', $datalt);
    		/* 导入jQuery的表单验证插件 */
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    
    		if($serve['state'])
    		{
    			 
    			$this->assign('state', array(
    					$serve['state']   => LANG::get('state_'.$serve['state']),
    			));
    		}else{
    			$this->assign('state', array(
    					'0'   => '未审核',
    					'1' => '正常',
    					'2'     => '注销',
    			));
    		}
    		 
    		$this->assign('acc',LANG::get('edit'));
    		if($this->serve_type==3)
    		{
    			$this->display('serve.form2.html');
    		}
    		else if($this->serve_type==4)
    		{
    			$this->display('serve.form3.html');
    		}
    		else{
    			if($_REQUEST['field_name']&& $_REQUEST['field_value']){
    				$this->assign('field',$_REQUEST);
    				//var_dump($_REQUEST);exit;
    			}
    			$this->display('serve.liangti.form.html');
    		}
    	}
    	else
    	{
    		$user_name=trim($_POST['phone_mob']);
    		    	$data = array(
    						'real_name'	=>	trim($_POST['ltname']),//量体师名
    						'gender'	=>	$_POST['gender'],//性别
    						'phone_mob'	=>	$user_name,//联系电话
    						'state_info'=>	$_POST['state'],//审核状态
    						'email' => $_POST['email'],
    						'figure_type' => 2,// 1店长2量体师
    						'serve_type'=>2,
    				);
    				if($_POST['password1']){
    					$data['password']=trim(md5($_POST['password1']));
    				}
    		/* 修改本地数据 */
    				if (!$this->_member->uniqueuser($user_name,$id))
    				{
    					$this->show_message('手机号已存在!');
    					return;
    				}
    				
    		   $editl=$this->_member->edit($id, $data);
    		   $ids=$this->_figure_liangti->get(array(
    		   		'conditions'=>"liangti_id='{$id}'",
    		   		'fields'=>'id',
    		   ));
    		   $idd=$ids['id'];
    		   $ident_card=trim($_POST['id_card']);
                  $datda=array(
                      'ident_card'	=>$ident_card,//身份证 
                  		'manager_id'	=>	$_POST['manager'],//量体师所属店长id
                        'card_number'   =>   trim($_POST['card_number']),
                  		'alone'=>$_POST['alone'],
		                       );
                 // if (!$this->_figure_liangti->uniquejob($job_number,$idd))
                  //{
                  //	$this->show_message('工号已存在!');
                  //	return;
                  //}
               //   if (!$this->_figure_liangti->uniqueid($ident_card,$idd))
               //   {
               //   	$this->show_message('身份证号已存在!');
               //   	return;
               //   }

          $editt=$this->_figure_liangti->edit($idd, $datda);
    		if($_POST['state']=='2'||$_POST['state']=='1')
    		{
    
    			$serve = $this->_serve->get_info($id);
    			$logdata['status']=$_POST['state'];
    			$logdata['mark']=$_POST['reason'];
    			if($serve)
    			{
    				$_applylog_mod=m('applylog');
    				@$_applylog_mod->edit($serve['userid'],$logdata);
    			}
    
    			if($_POST['state']=='2')
    			{
    
    
    				$m_user_id=$serve['userid'];
    				$m_serve_type=$serve['serve_type'];
    				$applylog_mod=m('applylog');
    				@$applylog_mod->drop('apply_type ='.$m_serve_type.' and user_id='.$m_user_id);
    					
    					
    				//申核失败没有删除
    				//$member_mod=m('member');
    				//$member_mod->edit('');
    					
    					
    				$_servedrop_mod=m('servedrop');
    				@$_servedrop_mod->add($serve);
    					
    					
    					
    					
    					
    				@$this->_serve->drop($id);
    					
    				$_servedetail_mod=m('servedetail');
    				@$_servedetail_mod->drop('idserve='.$id);
    					
    					
    					
    					
    				$reject_reason=$_POST['reason'];
    				$content='';
    				if($serve['serve_type']==2)
    				{
    					$content = get_msg('toseller_serve_2_refused_notify', array('reason' => $reject_reason));
    				}elseif($serve['serve_type']==3){
    					$content = get_msg('toseller_serve_3_refused_notify', array('reason' => $reject_reason));
    				}elseif($serve['serve_type']==4){
    					$content = get_msg('toseller_serve_4_refused_notify', array('reason' => $reject_reason));
    				}
    				$ms =ms();
    				$ms->pm->send(MSG_SYSTEM, $serve['userid'], '', $content);
    					
    					
    				$this->show_message('修改成功',
    						'back_list',    'index.php?app=serve&act=quantity'
    				);
    				return ;
    			}elseif($_POST['state']=='1'&&$_POST['ostate']=='0'){
    				$content='';
    				if($serve['serve_type'] == 4){
    					$content = get_msg('toseller_serve_4_passed_notify');
    				}
    				$ms =& ms();
    				$ms->pm->send(MSG_SYSTEM, $serve['userid'], '', $content);
    				//
    				$_member_mod=m('member');
    				$_member_mod->edit($serve['userid'],array('serve_type'=>$serve['serve_type']));
    					
    			}
    
    
    		}
    
    		$this->show_message('修改成功',
    				'back_list',    "index.php?app=serve&act=quantity&field_name={$_GET['field_name']}&field_value={$_GET['field_value']}",
    				'再次编辑',   'index.php?app=serve&amp;act=edit&amp;id=' . $id.'&t='.$_GET['t']
    		);
    	}
    }
    function droplt()
    {
    	$id = isset($_GET['id']) ? trim($_GET['id']):'';
    	if (!$id)
    	{
    		$this->show_warning('没有对应的用户id');
    		return;
    	}
    	//$ids = explode(',', $id);
    	
    	$liang=$this->_figure_liangti->get(array(
    			'conditions'=>"liangti_id='{$id}'",
    			 
    	));
    	$liangs=$liang['id'];
    	
    	if (!$this->_figure_liangti->drop($liangs))
    	{
    		$this->show_warning($this->_figure_liangti->get_error());
    		return;
    	}
    	if (!$this->_member->drop("user_id={$id}"))
    	{
    		$this->show_warning($this->_member->get_error());
    		return;
    	}
    	
    	$this->show_message('删除成功');
    }
    
    function ltinfo()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']):'';
        if (!$id)
        {
            $this->show_warning('没有对应的用户id');
            return;
        }
        //基本信息 start 
        $liang=$this->_figure_liangti->get(array(
            'conditions'=>"liangti_id='{$id}'",
        
        ));
        $user = $this->_member->get(array(
            'conditions' =>"figure_type=2 AND serve_type=2 AND user_id = '{$id}' " ,
            'join' =>"has_figurel",
            'fields'=>'user_name,real_name,money,state_info,gender,phone_mob',
          
        ));
        //所属门店
        $manager2=$this->_serve->find(array(
            'conditions'=>'1=1',
            'fields'=>'serve_name,userid',
            'index_key'=>'userid',
        ));
        
        $sex = array(1=>'男',2=>'女');
        $al  = array(1=>'是',2=>'否');
        
        $liang['real_name'] = $user['real_name'];
        $liang['user_name'] = $user['user_name'];
        $liang['gender'] = $sex[$user['gender']];
        $liang['money'] = $user['money'];
        $liang['alone'] = $al[$liang['alone']];
        $liang['manager_name'] = $manager2[$liang['manager_id']]['serve_name'];
        // 总收入
        $fees = $this->_figure_cs_mod->find(array(
            'conditions' =>"liangti_id ='{$id}' AND liangti_state != 3",
            'fields'     =>"service_mode",
            'index_key'  =>'',
        ));
       
        if($fees)
        {
            foreach ($fees as $k=>$v){
            
                $fees[$k]['single_fee'] =$this->_type[$v['service_mode']]['single'];
                $sum +=  $fees[$k]['single_fee'];
            }
           
            $liang['sum'] = $liang['money']+ $sum;
            
        }else
        {
            $liang['sum'] = $user['money'];
            
        }
        // 业务记录
        $page = $this->_get_page(5);
        $liangti_res = $this->_figure_cs_mod->find(array(
            'conditions' =>"liangti_id ='{$id}'",
            'fields'     =>"*",
            'order'         =>" lasttime DESC",
            'count'         => true,
            'limit' => $page['limit'],
        ));
        $page['item_count'] = $this->_figure_cs_mod->getCount();
      
        $this->_format_page($page);

        $this->assign('page_info', $page);
        $xb = array('10040'=>'男','10041'=>'女');
        $serve_type = array(1=>'预约上门',2=>'预约到店',3=>'线下采集',4=>'后台录入',6=>'指派量体师');
       
        foreach ($liangti_res as $key => $value) {
            $liangti_res[$key]['gender'] =$xb[$value['gender']];
            $liangti_res[$key]['service_mode'] =$serve_type[$value['service_mode']];
            $liangti_res[$key]['single_fee'] = $this->_type[$value['service_mode']]['single'];
            $liangti_res[$key]['lasttime'] = date("Y-m-d H:i:s",$value['lasttime']);
            // 管理 订单 
            if($value['son_sn'])
            {
                $ominfo = $this->_figure_om_mod->get("son_sn ='{$value['son_sn']}'");
                $liangti_res[$key]['order_sn'] = $ominfo['order_sn'];
                $goodsc = $this->_order_goods_mod->find(array(
                    'conditions' =>"order_id = '{$ominfo['order_id']}'",
                    'fields'    =>"cloth",
                ));
                foreach ($goodsc as $kk =>$vv){
                    import("diys.lib");
                    $diy = new Diys();
                    $r = $diy->_customs();
                    $goodsc[$kk]['cate_name'] = $r[$vv['cloth']]['cate_name'];
                    
                }
                $liangti_res[$key]['pl'] =  $goodsc;
                
            }else
            {
                $liangti_res[$key]['order_sn'] = '暂无';
              
                
            }
           
        }
      
       
        // 提现记录
        $cash_log = $this->_figure_cash_mod->find(array(
            'conditions' =>"user_id ='{$id}'",
            'fields'     =>"*",

        ));
        $status = array(0=>'提现中',1=>'提现成功',2=>'提现失败');
        foreach ($cash_log as $key => $val) {

            $cash_log[$key]['audit_time'] = date("Y-m-d H:i:s",$val['audit_time']);
            $cash_log[$key]['status']     = $status[$val['status']];
        }
        
        $this->assign('cash_log', $cash_log);
        $this->assign('res', $liangti_res);
        $this->assign('liang', $liang);
        $this->display('serve.liangti.info.html');
        
        
        
    }
    
    /**
     *ajax获得三级联动
     *@author liang.li <1184820705@qq.com>
     *@2015年4月29日
     */
    function get_region()
    {
        $region_mod = m('region');
        $pid = $_POST['pid'];
        if (!$pid)
        {
            $this->json_error('失败');
        }
         
        $list = $region_mod->get_options_html($pid,0);
        $this->json_result($list);
         
    }
    
    /**
    *格式化region表的is_serve字段
    *@author liang.li <1184820705@qq.com>
    *@2015年5月19日
    */
    function formatRegion() 
    {
        $list = $this->_serve->find();
        $region_mod = m('region');
        $region_mod->edit("1=1",array('is_serve'=>0));
        foreach ($list as $key => $value) 
        {
            $region_id = $value['region_id'];
            $region_info = $region_mod->get($region_id);
            $p_id = $region_info['parent_id'];
            $region_mod->edit($region_id,array('is_serve'=>1));
            $region_mod->edit($p_id,array('is_serve'=>1));
        }
    }
    
}

?>
