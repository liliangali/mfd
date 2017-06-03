
<?php
/*
 * shaozizhen@门店量体数据
 * */
class FigureApp extends BackendApp
{
	var $_customer_figure;
	var $_serve;
	var $_member;
	var $_figure_edit;
	var $_figure_om_mod;
	var $_order_mod;
	private $style;
    function __construct()
    {
    	parent::__construct();
        $this->_customer_figure=m('customer_figure');
        $path = ROOT_PATH."/data/config/serve_type.php";
        file_exists($path) && $this->_type = include $path;
        $this->_serve=m('serve');
        $this->_member=m('member');
        $this->_figure_edit=m('figure_edit');
        $this->_figure_om_mod  =& m('figureorderm');
        $this->_order_mod      =& m('order');
        $this->style = array(
        		'lw' => "领围",
                'xw'    => "胸围",
                'zyw'     => "中腰围",
                'tw'    => "臀围",
        		'dkzkc'    => "短裤左裤长",
        		'dkykc'    => "短裤右裤长",
				//'stw'    => "上臀围",
            	'zjk'    => "总肩宽",
            	'yxc'    => "右袖长",
            	'zxc'    => "左袖长",
            	'qjk'    => "前肩宽",
                'sbw'    => "上臂围",
            	'zww'    => "左腕围",
            	'yww'    =>"右腕围",
            	//'fw'    => "腹围",
            	'hyc'    => "后衣长",
	            'yw'    => "腰围",
	            'hd'    => "领围",//
	            'td'    => "通裆",
	            'hyg'    => "后腰高",
	            'qyg'    => "前腰高",
	            'kk'    => "领围",//
            	'height'    => "身高",
            	'weight'    => "体重",
            	'hyjc'    => "后腰节长",
	            'tgw'    => "腿根围",
	            'qyj'    => "前腰节",
	            'ykc'    => "右裤长",
	            'zkc'    => "左裤长",
	            'xiw'    => "膝围",
            	'jk'     => "脚口",
           'body_type_19'=>"左肩的特体数据",
            'body_type_20'=>"右肩的特体数据",
            'body_type_23'=>"背的特体数据",
           'body_type_24'=>"肚的特体数据",
            'body_type_25'=>"手臂的特体数据",
            'body_type_26'=>"臀的特体数据",
            'body_type_3'=>"上衣的着装风格",
            'body_type_2000'=>"西裤的着装风格",
            'body_type_3000'=>"衬衣的着装风格",
        	'body_type_4000'=>"马甲的着装风格",
            'body_type_6000'=>"大衣的着装风格",
            'body_type_11000'=>"女衬衣的着装风格",
            'body_type_15000'=>"男短裤的着装风格",
            'body_type_95000'=>"女西服的着装风格",
            'body_type_98000'=>"女西裤的着装风格",
        	'styleLength'     => "大衣款式款",//长、正常、短
        	'styleDY'     => "大衣着装习惯",// 穿大衣套西服,穿大衣套不西服)
        	'syzxc'     => "上衣左袖长",
        	'cyzxc'     => "衬衣左袖长",
        	'dyzxc'     => "大衣左袖长",
        	'syyxc'     => "上衣右袖长",
        	'cyyxc'     => "衬衣右袖长",
        	'dyyxc'     => "大衣右袖长",
        	'syhyc'     => "上衣后衣长",
        	'cyhyc'     => "衬衣后衣长",
        	'dyhyc'     => "大衣后衣长",
        		
    );          
    }

    function index()
    {
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => $_GET['field_name'],
                'name'  => 'field_value',
                'equal' => 'like',
            ),
        ));
    
        if($_GET['order_name'] && $_GET['order_name'] !='all')
        {
        	
        	if($_GET['order_name'] == "1")
        	{
                $conditions.=" AND service_mode=1 || service_mode=2 ";
        	}else
        	{
                $conditions.=" AND service_mode= {$_GET['order_name']} ";
        	}
        }
    	if($_GET['serve_name'] && $_GET['serve_name'] !='all')
    	{
    		$conditions.= " AND id_serve = {$_GET['serve_name']}";
    	}
    	if($_GET['cash_name'] && $_GET['cash_name'] !='all'){
    	    if($_GET['cash_name'] ==3){
    	        $conditions.=" AND liangti_state=3";
    	    }else{
    	        $conditions.=" AND liangti_state !=3";
    	    }
    	}
    	
        $page = $this->_get_page(30);
  
        $sql="SELECT figure_sn,gender,service_mode,liangti_id,customer_name,customer_mobile,lasttime,id_serve,liangti_state,liangti_name FROM(select * from cf_customer_figure where figure_state=1 AND (liangti_id != 0 OR id_serve != 0 ) {$conditions} ORDER BY figure_sn DESC) AS T
                      GROUP BY T.liangti_id,T.customer_mobile ORDER BY lasttime DESC LIMIT {$page['limit']}";
        $sql_count="SELECT figure_sn FROM(select * from cf_customer_figure where figure_state=1 AND (liangti_id != 0 OR id_serve != 0 ) {$conditions} ORDER BY figure_sn DESC) AS T
        GROUP BY T.liangti_id,T.customer_mobile ORDER BY figure_sn DESC";
        $db = &db();
        $r=$db->query($sql);
        $sql_c=$db->query($sql_count);
        $figures = array();
        $sql_counts = array();
        while($row=@mysql_fetch_assoc($r)){
        	$figures[]=$row;
        }
        while($rows=@mysql_fetch_assoc($sql_c)){
        	$sql_counts[]=$rows;
        }
        
        $counts=count($sql_counts);
        $serve = $this->_serve->find(array(
        		'conditions'=>"1=1",
        		'fields' =>"serve_name,userid",
        ));
        //var_dump($figures);exit;
        foreach ($figures as $key=>$val)
        {
            //$figures[$key]['single_fee'] = $this->_type[$val['service_mode']]['single'];
            if($val['liangti_id'] ==0)
            {
            	if(!empty($serve[$val['id_serve']]['userid']) && $serve[$val['id_serve']]['userid'] !=0){
            		$figuress[$serve[$val['id_serve']]['userid']][$val['customer_mobile']]=$val;
            		$figuress[$serve[$val['id_serve']]['userid']][$val['customer_mobile']]['single_fee'] = $this->_type[$val['service_mode']]['single'];
            	}
            	
            }else{
            	$figuress[$val['liangti_id']][$val['customer_mobile']]=$val;
            	$figuress[$val['liangti_id']][$val['customer_mobile']]['single_fee'] = $this->_type[$val['service_mode']]['single'];
            }
            	
            	 
            
           
        } 
        foreach($figuress as $key=>$val){
        	
        	foreach($val as $k=>$v)
        	{
        		$figuressy[]=$v;
        	}
        }
       $this->assign('figures', $figuressy);
      
       foreach($serve as $key=>$val){
       	$serve[$key]=$val['serve_name'];
       }
       $serve[1]='后台录入';
       $this->assign('serve', $serve);

       $this->assign('query_fields', array(
       		'customer_name' => '顾客姓名',
       		'customer_mobile'=>'联系电话',
       		'liangti_name'=>'量体师姓名',
       ));
      
       $this->assign('query_order', array(
       		'all' =>'全部',
       		'1' => '订单预约',//1 and 2
       		'3'=>'线下采集',
       		'4'=>'后台录入',
       		'6'=>'指定量体师',
       ));
       $this->assign('query_cash', array(
           'all' =>'全部',
           '1' => '待结算',//1 and 2
           '3'=>'已结算',
          
       ));
        $page['item_count'] = $counts;
       
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->display('figure.index.html');
    }
    
	/*function drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_user_to_drop');
            return;
        }
        

        $ids = explode(',', $id);

        
        if (!$this->_customer_figure->drop($ids))
        {
            $this->show_warning($this->_customer_figure->get_error());

            return;
        }

        $this->show_message('drop_ok');
    }*/
    
    function check()
    { 
            $data['body_type_26']=$_POST['body_type_26'];
            $data['body_type_3']=$_POST['body_type_3'];
            $data['body_type_2000']=$_POST['body_type_2000'];
            $data['body_type_11000']=$_POST['body_type_11000'];
            $data['body_type_15000']=$_POST['body_type_15000'];
            $data['body_type_3000']=$_POST['body_type_3000'];
            $data['body_type_6000']=$_POST['body_type_6000'];
            $data['body_type_95000']=$_POST['body_type_95000'];
            $data['body_type_98000']=$_POST['body_type_98000'];
    	$this->assign('cst_type', array(
    			    '10087'=>'正常-A',
    				'10093'=>'轻微溜肩-B',
    				'10068'=>'中度溜肩-C',
    				'10069'=>'严重溜肩-D',
    				'10094'=>'轻微耸肩-E',
    				'10072'=>'中度耸肩-F',
    				'10073'=>'严重耸肩-G',
    				'10088'=>'正常-A',
    				'10095'=>'轻微溜肩-B',
    				'10070'=>'中度溜肩-C',
    				'10071'=>'严重溜肩-D',
    				'10096'=>'轻微耸肩-E',
    				'10074'=>'中度耸肩-F',
    				'10075'=>'严重耸肩-G',
    				'10097'=>'正常-A',
    				'10098'=>'轻微驼背-B',
    				'10099'=>'中度驼背-C',
    				'10100'=>'严重驼背-D',
    				'10090'=>'正常',
    				'10079'=>'凸肚',
    				'10091'=>'正常',
    				'10080'=>'手臂靠前',
    				'10089'=>'轻微手臂靠后',
    				'10081'=>'严重手臂靠后',
    				'10092'=>'正常',
    			    '10082'=>'严重凸臀',
    				'10083'=>'严重坠臀',
    				'10084'=>'严重平臀',
    			    '10284'   => '正常',
    			    '10280' => '非常修身',
    			    '10281' => '很修身',
    			    '10282' => '修身',
    			    '10283' => '正常偏瘦',
    			    '10285' => '正常偏肥',
    			    '10286' => '宽松',
    			    '10287' => '很宽松',
    			    '10288' => '非常宽松', 
    	));
    	
    	$serve = $this->_serve->find(array(
    			'conditions'=>"1=1",
    			'fields' =>"serve_name",
    	));
    	foreach($serve as $key=>$val){
    		$serve[$key]=$val['serve_name'];
    	}
    	$this->assign('serve', $serve);
    	$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
    	$figure = $this->_customer_figure->get_info($id);
    	
    	$figure_edit=$this->_figure_edit->find(array(
    			'conditions' =>"figure_sn='{$figure['figure_sn']}'",
    			'index_key'=>"",
    	));
    	foreach($figure_edit as $key=>$val){
    		$figure_edit[$key][edit_content] = explode(",",$val['edit_content']);
    	}
    	 
    	$this->assign('figure_edits', $figure_edit);
    	$this->assign('styles', $this->style);
    	
    	
    	if (!$figure)
    	{
    		$this->show_warning('user_empty');
    		return;
    	}
    
    	if($figure['liangti_id']==0 && $figure['id_serve'] !=0){
    		
    		$servess = $this->_serve->get(array(
    				'conditions'=>"idserve={$figure['id_serve']}",
    				'fields' =>"userid",
    		));
    		$phone=$this->_member->get(array(
    				'conditions' => "user_id={$servess['userid']}",
    				'fields'  => "phone_mob,phone_tel,user_name",
    		));
    	}elseif($figure['liangti_id'] !=0){
    		
    		$phone=$this->_member->get(array(
    				'conditions' => "user_id={$figure['liangti_id']}",
    				'fields'  => "phone_mob,phone_tel,user_name",
    		));
    	}else{
    		$mobile='';
    	}
    	
    	if($phone['phone_tel']){
    		$mobile=$phone['phone_tel'];
    	}elseif($phone['phone_mob']){
    		$mobile=$phone['phone_mob'];
    	}elseif($phone['user_name']){
    		$mobile=$phone['user_name'];
    	}
    
    	$this->assign('mobile',$mobile);
    	
    	$figure['lasttime']= date('Y-m-d H:i',$figure['lasttime']);
    	$figure['single_fee'] = $this->_type[ $figure['service_mode']]['single'];
    	//订单号  订单状态
    	
    	$fm = $this->_figure_om_mod->get("son_sn = '{$figure['son_sn']}'");
    	$figure['order_sn'] = $fm['order_sn'];
    	$orderinfo =  $this->_order_mod->get("order_sn ='{$figure['order_sn']}'");
    	$sta = array(12=>'未付款',20=>'已付款',30=>'已发货',40=>'已完成',0=>'已取消',50=>'待量体',60=>'生产中',70=>'退款中',80=>'已退款');
    	$figure['order_zt'] = $sta[$orderinfo['status']];
    	$this->assign('figure', $figure);
    	
    	$this->assign('cst_style', array(
    			'10284'   => '正常',
    			'10280' => '非常修身',
    			'10281' => '很修身',
    			'10282' => '修身',
    			'10283' => '正常偏瘦',
    			'10285' => '正常偏肥',
    			'10286' => '宽松',
    			'10287' => '很宽松',
    			'10288' => '非常宽松',
    	));
    	
    	$this->display('figure.check.html');
    }
    
    
	function edit()
    {
    	$serve = $this->_serve->find(array(
    			'conditions'=>"1=1",
    			'fields' =>"serve_name",
    	));
    	foreach($serve as $key=>$val){
    		$serve[$key]=$val['serve_name'];
    	}
    	$this->assign('serve', $serve);
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 是否存在 */
            $figure = $this->_customer_figure->get_info($id);
            if (!$figure)
            {
                $this->show_warning('user_empty');
                return;
            }
            
            
            if($figure['liangti_id']==0 && $figure['id_serve'] !=0){
            
            	$servess = $this->_serve->get(array(
            			'conditions'=>"idserve={$figure['id_serve']}",
            			'fields' =>"userid",
            	));
            	$phone=$this->_member->get(array(
            			'conditions' => "user_id={$servess['userid']}",
            			'fields'  => "phone_mob,phone_tel,user_name",
            	));
            }elseif($figure['liangti_id'] !=0){
            
            	$phone=$this->_member->get(array(
            			'conditions' => "user_id={$figure['liangti_id']}",
            			'fields'  => "phone_mob,phone_tel,user_name",
            	));
            }else{
            	$mobile='';
            }
             
            if($phone['phone_tel']){
            	$mobile=$phone['phone_tel'];
            }elseif($phone['phone_mob']){
            	$mobile=$phone['phone_mob'];
            }elseif($phone['user_name']){
            	$mobile=$phone['user_name'];
            }
            
            $this->assign('mobile',$mobile);
            $figure['lasttime']= date('Y-m-d H:i',$figure['lasttime']);
            $figure['single_fee'] = $this->_type[ $figure['service_mode']]['single'];
            //订单号  订单状态
            
            $fm = $this->_figure_om_mod->get("son_sn = '{$figure['son_sn']}'");
            $figure['order_sn'] = $fm['order_sn'];
            $orderinfo =  $this->_order_mod->get("order_sn ='{$figure['order_sn']}'");
            $sta = array(12=>'未付款',20=>'已付款',30=>'已发货',40=>'已完成',0=>'已取消',50=>'待量体',60=>'生产中',70=>'退款中',80=>'已退款');
            $figure['order_zt'] = $sta[$orderinfo['status']];
            $this->assign('figure', $figure);

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('figure_type', array(
	            '0'   => LANG::get('figure_type_0'),
	            '1' => LANG::get('figure_type_1'),
        	));
            $this->assign('cst_cate', array(
            		 
            		'3' => '西服',
            		'2000' => '西裤',
            		'4000' => '马夹',
            		'3000' => '衬衣',
            		'6000' => '大衣',
            		'11000' => '女衬衣',
            		'95000' => '女西服',
            		'98000' => '女西裤',
            ));
            $this->assign('cst_style', array(
            		'10284'   => '正常',
            		'10280' => '非常修身',
            		'10281' => '很修身',
            		'10282' => '修身',
            		'10283' => '正常偏瘦',
            		'10285' => '正常偏肥',
            		'10286' => '宽松',
            		'10287' => '很宽松',
            		'10288' => '非常宽松',
            ));
        	$this->assign('acc',LANG::get('edit'));
        	
        	$figure_edit=$this->_figure_edit->find(array(
        			'conditions' =>"figure_sn='{$figure['figure_sn']}'",
        					'index_key'=>"",
        	));
        	foreach($figure_edit as $key=>$val){
        		$figure_edit[$key]['edit_content'] = explode(",",$val['edit_content']);
        	}
        	
        	$this->assign('figure_edits', $figure_edit);
        	$this->assign('styles', $this->style);
        
        	$figure_mode=array('1'=>LANG::get('figure_mode_1'),'2'=>LANG::get('figure_mode_2'));
            
            $this->assign('figure_mode', $figure_mode);
        	
            $this->display('figure.form.html');
        }
        else
        {
        	
        	$user=$this->visitor->get();
        	$figure = $this->_customer_figure->get_info($id);
        	
        	foreach($_POST as $key=>$val){
        		if($figure[$key] !=$val){
        			$data[$key]=$val;
        		}
        	}
        	$data_1=$data;
        	$data_1['lasttime']= time();
        	if($data){
        		$data_c= implode(",", array_keys($data));
        	}
            /* 修改本地数据 */
        	if($data_1){
           $edit= $this->_customer_figure->edit($id, $data_1);
        	}
            if($edit){
	          
	            
	             $data_ed=array(
	             		'liangti_id'=>$user['user_id'],
	             		'liangti_name'=>$user['user_name'],
	             		'time' =>time(),
	             		'edit_content'=>$data_c,
	             		'figure_sn' =>$figure['figure_sn'],
	             );
	             $this->_figure_edit->add($data_ed);
              }
            

            $this->show_message('修改成功',
                'back_list',    'index.php?app=figure',
                '再次编辑',   'index.php?app=figure&amp;act=edit&amp;id=' . $id
            );
        }
    }
    
    function check_user_name(){
    	$user_name = empty($_GET['user_name']) ? null : trim($_GET['user_name']);
		if (!$user_name)
		{
			echo ecm_json_encode(false);
			return ;
		}
		
		$member_mod=m('member');
		$res=$member_mod->unique($user_name);
		if($res)
		{
			echo ecm_json_encode(false);
			return ;
		}
		
		
		echo ecm_json_encode($this->_customer_figure->unique($user_name));
    }
    
	function add()
    {
        if (!IS_POST)
        {
        	
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
			$this->assign('figure_type', array(
	            '0'   => LANG::get('figure_type_0'),
	            '1' => LANG::get('figure_type_1'),
        	));
        	$this->assign('cst_cate', array(
	      
	            '3' => '西服',
        	'2000' => '西裤',
        	'4000' => '马夹',
        	'3000' => '衬衣',
        	'6000' => '大衣',
        	'11000' => '女衬衣',
        	'95000' => '女西服',
        	'98000' => '女西裤',
        	));
        	$this->assign('cst_style', array(
        			'10284'   => '正常',
        			'10280' => '非常修身',
        			'10281' => '很修身',
        			'10282' => '修身',
        			'10283' => '正常偏瘦',
        			'10285' => '正常偏肥',
        			'10286' => '宽松',
        			'10287' => '很宽松',
        			'10288' => '非常宽松',
        	));
        	$this->assign('acc',LANG::get('add'));
        	
        	$figure_mode=array('1'=>LANG::get('figure_mode_1'),'2'=>LANG::get('figure_mode_2'));
            
            $this->assign('figure_mode', $figure_mode);
        	
            $this->display('figure.form.html');
        }
        else
        {

        	$user=$this->visitor->get();
            $data = array(
                'lw' => $_POST['lw'],
                'xw'    => $_POST['xw'],
                'zyw'     => $_POST['zyw'],
                'tw'    => $_POST['tw'],
				//'stw'    => $_POST['stw'],
            	'zjk'    => $_POST['zjk'],
            	'yxc'    => $_POST['yxc'],
            	'zxc'    => $_POST['zxc'],
            	'dkzkc'=>$_POST['dkzkc'],
            	'dkykc'=>$_POST['dkykc'],
            	'qjk'    => $_POST['qjk'],
                'sbw'    => $_POST['sbw'],
            	'zww'    => $_POST['zww'],
            	'yww'    => $_POST['yww'],
            	//'fw'    => $_POST['fw'],
            	'hyc'    => $_POST['hyc'],
	            'yw'    => $_POST['yw'],
	            'hd'    => $_POST['hd'],
	            'td'    => $_POST['td'],
	            'hyg'    => $_POST['hyg'],
	            'qyg'    => $_POST['qyg'],
	            'kk'    => $_POST['kk'],
            	'height'    => $_POST['height'],
            	'weight'    => $_POST['weight'],
	            'service_mode'    => 4 ,
            	'id_serve' => 1,
            	'figure_state' =>1,
            	'firsttime' => time(),
            	'liangti_name' => $user['user_name'],
            	'liangti_id' =>$user['user_id'],
            	'lasttime'  => time(),
                'customer_name'    => $_POST['customer_name'],
            	'customer_mobile'    => $_POST['customer_mobile'],
            	'gender'    => $_POST['gender'],
            	'hyjc'    => $_POST['hyjc'],
	            'tgw'    => $_POST['tgw'],
	            'qyj'    => $_POST['qyj'],
	            'ykc'    => $_POST['ykc'],
	            'zkc'    => $_POST['zkc'],
	            //'xiw'    => $_POST['xiw'],
            	//'jk'     => $_POST['jk'],
            	'styleLength'     => $_POST['styleLength'],//长、正常、短
            	'styleDY'     =>$_POST['styleDY'],// 穿大衣套西服,穿大衣套不西服)
            	'syzxc'     => $_POST['syzxc'],
            	'cyzxc'     => $_POST['cyzxc'],
            	'dyzxc'     => $_POST['dyzxc'],
            	'syyxc'     => $_POST['syyxc'],
            	'cyyxc'     => $_POST['cyyxc'],
            	'dyyxc'     => $_POST['dyyxc'],
            	'syhyc'     => $_POST['syhyc'],
            	'cyhyc'     => $_POST['cyhyc'],
            	'dyhyc'     => $_POST['dyhyc'],
            
            );
            if($_POST['xiw']){
            	$data['xiw']=$_POST['xiw'];
            }
            if($_POST['jk']){
            	$data['jk']=$_POST['jk'];
            }
            $data['body_type_19']=$_POST['body_type_19'];
            $data['body_type_20']=$_POST['body_type_20'];
            $data['body_type_23']=$_POST['body_type_23'];
            $data['body_type_24']=$_POST['body_type_24'];
            $data['body_type_25']=$_POST['body_type_25'];
            $data['body_type_26']=$_POST['body_type_26'];
            $data['body_type_3']=$_POST['body_type_3'];
            $data['body_type_2000']=$_POST['body_type_2000'];
            $data['body_type_3000']=$_POST['body_type_3000'];
            $data['body_type_4000']=$_POST['body_type_4000'];
            $data['body_type_6000']=$_POST['body_type_6000'];
            $data['body_type_11000']=$_POST['body_type_11000'];
            $data['body_type_15000']=$_POST['body_type_15000'];
            $data['body_type_95000']=$_POST['body_type_95000'];
            $data['body_type_98000']=$_POST['body_type_98000'];            
            $data['part_label_10130']=$_POST['part_label_10130'];
            $data['part_label_10131']=$_POST['part_label_10131'];
            $data['part_label_10725']=$_POST['part_label_10725'];
            $data['part_label_10726']=$_POST['part_label_10726'];
            
            $member_mod=m('member');
            $res=$member_mod->get(array(
            'fields'=>'user_id',
            'conditions'=>"user_name='".$_POST['user_name']."'",
            ));
            
            $data['userid']=$res['user_id'];
            $idfigure=$this->_customer_figure->add($data);
            if (!$idfigure)
            {
                $this->show_warning($this->_customer_figure->get_error());
                return;
            }
            $this->show_message('添加成功',
                'back_list',    'index.php?app=figure',
                'continue_add', 'index.php?app=figure&amp;act=add'
            );
        }
    }
    
    
    function export (){
    	 
    	$customers =$this->_customer_figure->findAll(array(
    			"conditions" => "",
    			'fields' => 'figure_sn,customer_name,gender,customer_mobile,lasttime,liangti_name,id_serve,service_mode',
    	));
    	foreach ($customers as &$row){
    		switch ($row['gender']){
    			case 10040:
    				$row['gender'] = '男';
    				break;
    			case 10041:
    				$row['gender'] = '女';
    				break;
    			default:
    				$row['gender'] = '未填写';
    				break;
    		}
    
    		switch ($row['id_serve']){
    			case 1:
    				$row['id_serve'] = '后台录入';
    				break;
    			case 162:
    				$row['id_serve'] = '时尚厅';
    				break;
    			case 169:
    				$row['id_serve'] = '商务厅';
    				break;
    			default:
    				$row['id_serve'] = '未知';
    				break;
    		}
    		switch ($row['service_mode']){
    			case 1:
    				$row['service_mode'] = '预约上门';
    				break;
    			case 2:
    				$row['service_mode'] = '预约到店';
    				break;
    			case 3:
    				$row['service_mode'] = '线下采集';
    				break;
    			case 4:
    				$row['service_mode'] = '后台录入';
    				break;
    			default:
    				$row['service_mode'] = '未知';
    				break;
    		}
    		$row['lasttime']=date("Y-m-d H:i:s",$row['lasttime']);
    	}
    
    	$fields_name = array('ID','顾客姓名','性别','手机号','提交时间','量体师','数据归属','服务方式');
    	array_unshift($customers,$fields_name);
    	$this->export_to_csv($customers, 'customer', 'gbk');
    }
}

?>
