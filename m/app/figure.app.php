<?php


class FigureApp extends MemberbaseApp
{
	var $_figure,$user_id,$idserve;
    function __construct()
    {
    	parent::__construct();
        $this->_figure=m('figure');
        
        /* 当前用户中心菜单 */
        $this->_curitem('figure');
    	$this->user_id = $this->visitor->get('user_id');
    	$this->idserve = $this->visitor->get('idserve');
    	$this->assign('title', 'RCTAILOR-酷客中心-量体数据');
    }

    function index()
    {
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'user_name',
                'name'  => 'user_name',
                'equal' => 'like',
            ),
        ));
    	
    	
    	//更新排序
        if (isset($_GET['sort']) && !empty($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'idfigure';
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
                $sort  = 'idfigure';
                $order = 'desc';
            }
        }
        
        $page = $this->_get_page();
        $figures = $this->_figure->find(array(
        	'conditions' => 'figure_type=1 and serve_userid ='.$this->user_id . $conditions,    
        	'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));

        $this->assign('figures', $figures);
        $page['item_count'] = $this->_figure->getCount();
        $this->_format_page($page);

        $this->assign('page_info', $page);
        
        //1上门、2到店
         $this->assign('figure_mode_arr', array('1'=>'上门','2'=>'到店'));
         
        $this->assign('query_fields', array(
            'idfigure' => LANG::get('idfigure')
        ));
        $this->assign('sort_options', array(
            'create_date DESC'   => LANG::get('create_date'),
            'idfigure DESC' => LANG::get('idfigure')
        ));
        $this->display('figure.index.html');
    }
	function drop()
    {
    	$args = $this->get_params();
    	
    	$id = empty($args[0]) ? 0 : intval($args[0]);
        //$id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_user_to_drop');
            return;
        }
        

        $ids = explode(',', $id);

        $ids=' idfigure in('.$id.') and serve_userid = '.$this->user_id;
        
        if (!$this->_figure->drop($ids))
        {
            $this->show_warning($this->_figure->get_error());

            return;
        }

        $this->show_message('drop_ok');
    }
    
	function edit()
    {
        $args = $this->get_params();
    	
    	$id = empty($args[0]) ? 0 : intval($args[0]);
        
        
        if (!IS_POST)
        {
            /* 是否存在 */
            $figure = $this->_figure->get_info($id);
            if (!$figure||$figure['serve_userid']!=$this->user_id)
            {
                $this->show_warning('user_empty');
                return;
            }
			
            
            $this->assign('figure', $figure);
			
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('figure_type', array(
	            '0'   => LANG::get('figure_type_0'),
	            '1' => LANG::get('figure_type_1'),
        	));
        	$this->assign('acc',LANG::get('edit'));
        	
        	$figure_mode=array('1'=>LANG::get('figure_mode_1'),'2'=>LANG::get('figure_mode_2'));
            
            $this->assign('figure_mode', $figure_mode);
            
        	
            $employee_mod=m('employee');
        		$employee_data=$employee_mod->find(array(
        		'fields'=>'employee_name',
        		'conditions' => ' idserve ='.$this->idserve  ,    
	        	'limit' => '0,10',
	            'order' => "idemployee desc",
        		));
        		
        		
        		foreach ($employee_data as $k=>$v)
        		{
					 $employee_name_arr[$employee_data[$k]['employee_name']]=$employee_data[$k]['employee_name'];		
        		}
        		//var_dump($employee_name_arr);exit;
        		$this->assign('employee_name_arr',$employee_name_arr);
            
            $this->display('figure.form.html');
        }
        else
        {
            $data = array(
                'lw' => $_POST['lw'],
                'xw'    => $_POST['xw'],
                'zyw'     => $_POST['zyw'],
                'tw'    => $_POST['tw'],
				'stw'    => $_POST['stw'],
            	'zjk'    => $_POST['zjk'],
            	'yxc'    => $_POST['yxc'],
            	'zxc'    => $_POST['zxc'],
            	'qjk'    => $_POST['qjk'],
            
            	'hyc'    => $_POST['hyc'],
	            'yw'    => $_POST['yw'],
	            'hd'    => $_POST['hd'],
	            'td'    => $_POST['td'],
	            'hyg'    => $_POST['hyg'],
	            'qyg'    => $_POST['qyg'],
	            'kk'    => $_POST['kk'],
            
            	'hyjc'    => $_POST['hyjc'],
	            'tgw'    => $_POST['tgw'],
	            'qyj'    => $_POST['qyj'],
	            'ykc'    => $_POST['ykc'],
	            'zkc'    => $_POST['zkc'],
	            'xiw'    => $_POST['xiw'],
            
	            //'figure_type'    => $_POST['figure_type'],
	            'height'    => $_POST['height'],
	            'weight'    => $_POST['weight'],
				//'userid'    => $_POST['userid'],
            	'serve_userid'    => $this->user_id,
            
                'figure_name'    => $_POST['figure_name'],
            	'employee_name'    => $_POST['employee_name'],
            	'figure_mode'    => $_POST['figure_mode'],
            
            );

            $data['body_type_19']=$_POST['body_type_19'];
            $data['body_type_20']=$_POST['body_type_20'];
            $data['body_type_24']=$_POST['body_type_24'];
            $data['body_type_25']=$_POST['body_type_25'];
            $data['body_type_26']=$_POST['body_type_26'];
            $data['body_type_3']=$_POST['body_type_3'];
            $data['body_type_2000']=$_POST['body_type_2000'];
            $data['styleLength']=$_POST['styleLength'];
            
            $data['part_label_10130']=$_POST['part_label_10130'];
            $data['part_label_10131']=$_POST['part_label_10131'];
            $data['part_label_10725']=$_POST['part_label_10725'];
            $data['part_label_10726']=$_POST['part_label_10726'];
            
            
            /* 修改本地数据 */
            $this->_figure->edit('serve_userid='.$this->user_id.' and idfigure='.$id, $data);

            
			
            $url=$this->_view->build_url(array('app'=>'figure'));
            
            $this->show_message('修改成功','go_back',$url);
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
		
		
		echo ecm_json_encode($this->_figure->unique($user_name));
    }
    
    function test_add()
    {
    	//$orderid,$serviceid
		if($_GET['orderid']&&$_GET['serviceid']){
			$orderid=$_GET['orderid'];
			$serviceid=$_GET['serviceid'];
			$figureorderlog_mod=m('figureorderlog');
			//var_dump($figureorderlog_mod);
			var_dump($figureorderlog_mod->addlog($orderid,$serviceid));
		}else 
		{
			echo '0';
		}
    }
    
	function add()
    {
    	
    	
    	

        if (!IS_POST)
        {
        	$args = $this->get_params();
        	if($args[0])
        	{
        		
        		
        		$userinfo=getUinfoByUid($args[0]);
        		$figure['userid']=$args[0];
        		$figure['user_name']=$userinfo['user_name'];
        		$this->assign('figure',$figure);
        	}
            
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
			$this->assign('figure_type', array(
	            '0'   => LANG::get('figure_type_0'),
	            '1' => LANG::get('figure_type_1'),
        	));
        	$this->assign('acc',LANG::get('add'));
        	
        	$figure['figure_type']='1';
        	$figure['unit']='cm';
        	
        	$this->assign('figure', $figure);
        	
        	$figure_mode=array('1'=>LANG::get('figure_mode_1'),'2'=>LANG::get('figure_mode_2'));
            
        	
        	
            $this->assign('figure_mode', $figure_mode);
            $this->assign('do', 'add');
        	
            	$employee_mod=m('employee');
        		$employee_data=$employee_mod->find(array(
        		'fields'=>'employee_name',
        		'conditions' => ' idserve ='.$this->idserve  ,    
	        	'limit' => '0,10',
	            'order' => "idemployee desc",
        		));
        		
        		
        		foreach ($employee_data as $k=>$v)
        		{
					 $employee_name_arr[$employee_data[$k]['employee_name']]=$employee_data[$k]['employee_name'];		
        		}
        		//var_dump($employee_name_arr);exit;
        		$this->assign('employee_name_arr',$employee_name_arr);
            
        	if($_GET['order_id'])
        	{
        		//var_dump($_GET['order_id']);
        		$this->assign('isorder',1);
        	}else 
        	{
				  $this->assign('isorder',0);      		
        	}
        	
            $this->display('figure.form.html');
        }
        else
        {

            //var_dump($_POST);exit;
			
            
            $data = array(
                'lw' => $_POST['lw'],
                'xw'    => $_POST['xw'],
                'zyw'     => $_POST['zyw'],
                'tw'    => $_POST['tw'],
				'stw'    => $_POST['stw'],
            	'zjk'    => $_POST['zjk'],
            	'yxc'    => $_POST['yxc'],
            	'zxc'    => $_POST['zxc'],
            	'qjk'    => $_POST['qjk'],
            
            	'hyc'    => $_POST['hyc'],
	            'yw'    => $_POST['yw'],
	            'hd'    => $_POST['hd'],
	            'td'    => $_POST['td'],
	            'hyg'    => $_POST['hyg'],
	            'qyg'    => $_POST['qyg'],
	            'kk'    => $_POST['kk'],
	            'figure_type'    => 1,
	            'height'    => $_POST['height'],
	            'weight'    => $_POST['weight'],
				//'userid'    => $_POST['userid'],
            	'serve_userid'    => $this->user_id,
            	
            	'figure_name'    => $_POST['figure_name'],
            	'employee_name'    => $_POST['employee_name'],
            	'figure_mode'    => $_POST['figure_mode'],
            
            	
            	'hyjc'    => $_POST['hyjc'],
	            'tgw'    => $_POST['tgw'],
	            'qyj'    => $_POST['qyj'],
	            'ykc'    => $_POST['ykc'],
	            'zkc'    => $_POST['zkc'],
	            'xiw'    => $_POST['xiw'],
            	
            	'user_name'    => $_POST['user_name'],
            	
            	
            		
            	
            );
            
            $member_mod=m('member');
            $res=$member_mod->get(array(
            'fields'=>'user_id',
            'conditions'=>"user_name='".$_POST['user_name']."'",
            ));
            
            $data['userid']=$res['user_id'];
            
            
            
            
            $data['idserve']=$this->idserve;
            
            $data['body_type_19']=$_POST['body_type_19'];
            $data['body_type_20']=$_POST['body_type_20'];
            $data['body_type_24']=$_POST['body_type_24'];
            $data['body_type_25']=$_POST['body_type_25'];
            $data['body_type_26']=$_POST['body_type_26'];
            $data['body_type_3']=$_POST['body_type_3'];
            $data['body_type_2000']=$_POST['body_type_2000'];
            $data['styleLength']=$_POST['styleLength'];
            
            //-----------------------
            
            $data['part_label_10130']=$_POST['part_label_10130'];
            $data['part_label_10131']=$_POST['part_label_10131'];
            $data['part_label_10725']=$_POST['part_label_10725'];
            $data['part_label_10726']=$_POST['part_label_10726'];
            
            //------------------------
            $res_user_name=$this->_figure->unique($_POST['user_name']);
            if($res_user_name)
            {
            	$idfigure=$this->_figure->add($data);
            }
            
            //var_dump($res);exit;
            //订单补录量体数据（不需要存量体表），
           	$orderid=isset($_POST['order_id'])?intval($_POST['order_id']):0;
            if($orderid)
            {
            	if(!$idfigure)
            	{
            		$idfigure=0;
            	}
            }
           	
           	
            if (!$idfigure&&!$orderid)
            {
                $this->show_warning('error');
                return;
            }else 
            {
            	$orderid=isset($_POST['order_id'])?intval($_POST['order_id']):0;
            	$serviceid=$this->idserve;
            	
            	$this->data_mod=m('figureorderlog');
            	$this->data_mod->addlog($orderid,$serviceid);
            	//分成记录
            	
            	
	    		//修改orderfigure量体数据
		    	if($orderid&&$serviceid)
		    	{
			    	$orderfigure_mod=m('orderfigure');
			    	$orderfigure_data= $orderfigure_mod->get(array(
			    		'conditions' => 'serviceid='.$serviceid.' and order_id ='.$orderid,
			    	));
			    	if($orderfigure_data&&!$orderfigure_data['lw']&&!$orderfigure_data['xw']&&!$orderfigure_data['zyw'])
			    	{
			    		$orderfigure_data_update=array('lw' => $_POST['lw'],
		                'xw'    => $_POST['xw'],
		                'zyw'     => $_POST['zyw'],
		                'tw'    => $_POST['tw'],
						'stw'    => $_POST['stw'],
		            	'zjk'    => $_POST['zjk'],
		            	'yxc'    => $_POST['yxc'],
		            	'zxc'    => $_POST['zxc'],
		            	'qjk'    => $_POST['qjk'],
		            
		            	'hyc'    => $_POST['hyc'],
			            'yw'    => $_POST['yw'],
			            'hd'    => $_POST['hd'],
			            'td'    => $_POST['td'],
			            'hyg'    => $_POST['hyg'],
			            'qyg'    => $_POST['qyg'],
			            'kk'    => $_POST['kk'],
				    		'height'    => $_POST['height'],
			            'weight'    => $_POST['weight'],
				    		'hyjc'    => $_POST['hyjc'],
			            'tgw'    => $_POST['tgw'],
			            'qyj'    => $_POST['qyj'],
			            'ykc'    => $_POST['ykc'],
			            'zkc'    => $_POST['zkc'],
			            'xiw'    => $_POST['xiw'],
			    		
			    		'body_type_19' =>  $data['body_type_19'],
						'body_type_20' =>  $data['body_type_20'],
						'body_type_24' =>  $data['body_type_24'],
						'body_type_25' =>  $data['body_type_25'],
						'body_type_26' =>  $data['body_type_26'],
						'body_type_3' =>  $data['body_type_3'],
						'body_type_2000' =>  $data['body_type_2000'],
						'styleLength' =>  $data['styleLength'],
						'figure_name' =>  $data['figure_name'],
						'figure'  => $idfigure,
			    		
			    		
			    		
			    		'part_label_10130' =>  $data['part_label_10130'],
			    		'part_label_10131' =>  $data['part_label_10131'],
			    		'part_label_10725' =>  $data['part_label_10725'],
			    		'part_label_10726' =>  $data['part_label_10726'],
			    		
			    		);
			    		$orderfigure_edit_res=$orderfigure_mod->edit('serviceid='.$serviceid.' and order_id ='.$orderid,$orderfigure_data_update);
			    	}else 
			    	{
			    		$this->show_message('订单量体数据重复编辑!');
			    	}
		    	}
	    	
            	
            	

            }
             $url=$this->_view->build_url(array('app'=>'figure'));
            $this->show_message('添加成功','go_back',$url);
        }
    }
}

?>
