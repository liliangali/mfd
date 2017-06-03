<?php
class ServiceApp extends MallbaseApp
{
	var $_serve,$_serve_mod;
    function __construct()
    {
    	parent::__construct();
        $this->_serve=m('serve');
        $this->_serve_mod = m('serve');
        
        
    }
	
	function apply()
	{
		if (!$this->visitor->has_login)
        {
            $this->show_warning('login_please');
            return;
        }
		
        $res=$this->_serve_mod->get(array(
    			'conditions' => 'userid = '.$this->visitor->info['user_id'],));
        if($res['idserve'])
        {
        	header('Location:/index.php/serve-apply3-'.$res['idserve'].'.html');
			return ;
        }
        
		/* 导入jQuery的表单验证插件 */
		$this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js,mlselection.js',
            'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'
            ));
			
            $region_mod =m('region');
            $this->assign('regions', $region_mod->get_options(0));
            
            $this->display('serve/serve.apply1.html');
	}
	
	
	function index()
	{
        $this->nindex();
        $this->assign('title',"量体服务");
        if($_GET['ajax']=='1')
		{
			return ;
		}else 
		{
			$this->display('serve/serve.search.html');
		}
	}
	
	
	function indexnew()
	{
		$this->nindex();
		if($_GET['ajax']=='1')
		{
			return ;
		}else
		{
			$this->display('serve/serve.indexnew.html');
		}
	}
	function ajax_index()
	{
		$this->nindex();
		$this->display('serve/serve.search_page.html');
	}
	
	function nindex()
	{
		$this->assign('title','量体服务网络');
		$this->assign('back_url', 1);	
    	$args = $this->get_params();
    	$argcount=count($args);
    	if($argcount>1)
    	{
    		$region_mod=m('region');
    		if($argcount==4)
    		{
    			if($args[3])
    			{
	    			$region_data=$region_mod->get_descendant(intval($args[3]));
	        		$this->assign('value', intval($args[3]));
	    			$this->assign('r2', intval($args[2]));
	    			$this->assign('r1', intval($args[1]));
    			}elseif($args[2])
    			{
    				$region_data=$region_mod->get_descendant(intval($args[2]));
	    			$this->assign('r2', intval($args[2]));
	    			$this->assign('r1', intval($args[1]));
    			}elseif($args[1])
    			{
    				$region_data=$region_mod->get_descendant(intval($args[1]));
    				$this->assign('r1', intval($args[1]));
    			}
    		}
    		
    		if($region_data)
    		{
    			$conditions=" and region_id in(".implode(',',$region_data).")";

    		}
    		
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
        
        if(isset($_GET['l'])&&$_GET['l']=='100')
        {
        	$page = $this->_get_page(100);
        }else 
        {
        	$page = $this->_get_page(5);
        }
        if($_GET['ajax']=='1'){
        	
	        if(isset($_GET['value']))
	        {
	        	$region_id = isset($_GET['value']) ? trim($_GET['value']) : '';
	        	$conditions=' and region_id='.$region_id;
	        }
        	
        	$serves = $this->_serve->find(array(
	        	'conditions' => 'serve_type=2 and state=1 ' . $conditions,    
	            'order' => "$sort $order",
	            'count' => true,
        		'fields'=>'idserve,serve_name,mobile,serve_address',
	        ));
	        
	        $this->json_result(array_values($serves));
	        return;
        }else{
        	
        	$serves = $this->_serve->find(array(
	        	'conditions' => 'serve_type=2 and state=1  ' . $conditions,    
	        	'limit' => $page['limit'],
	            'order' => "$sort $order",
	            'count' => true,
	        ));
        }
        
        $this->assign('serves', $serves);
        $page['item_count'] = $this->_serve->getCount();
        $this->_format_page($page);
        
        $this->assign('page_info', $page);
        
		/* 导入jQuery的表单验证插件 */
		$this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,mlselection.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
		'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'
            ));
		
		$region_mod  =& m('region');
        $this->assign('regions', $region_mod->get_options(0));
	}
	
	
	function info()
	{
		
		
		
		
		
		$args = $this->get_params();
		
		
		$serve=m('serve');
        $res=$serve->find(array(
    			//'conditions' => '1=1 and serve_detail.longitude  is not null ',
    			'conditions' => 'serve.idserve = '.$args[0],
        		'limit' => '0,10',
				'join' => 'has_serve_detai',));
			//var_dump($res);
		$this->assign('serves', $res);
		
		if($args[0]){
			
			
			
			$resserve=$this->_serve->get(array(
    			'conditions' => 'serve.idserve = '.$args[0],
				'join' => 'has_serve_detai',));
			//var_dump($res);
			
			
			$serverate_mod=m('serverate');
			
			$serverate_data=$serverate_mod->getrow('SELECT AVG(point) point,count(1) cot,(sum(point)/(count(1)*5))*100 coa  FROM rc_serve_rate where idserve='.$args[0]);
			//var_dump($serverate_data);exit;
			
			$resserve['point']=doubleval($serverate_data['point']);//
			//$resserve['point']=1234.5678;
			if($resserve['point'])
			{
			$resserve['point']=number_format($resserve['point'], 1, '.', '');
			}
			
			$resserve['cot']=doubleval($serverate_data['cot']);
			$resserve['coa']=doubleval($serverate_data['coa']);//
			if($resserve['coa']){
			$resserve['coa']=number_format($resserve['coa'], 1, '.', '');
			}
			//var_dump($serverate_data);
			$resserve['html_entity_decode_introduce']=html_entity_decode($resserve['introduce']);
			
			$this->assign('serve', $resserve);
			$this->assign('region',explode(' ',$resserve['region_name']));
			
			$this->assign('title',"量体服务-".$resserve['serve_name']."-西服定制中心、男士正装定制、国际高端西服品牌|RCRAILOR");
			
			
		}
		
		
		//var_dump($args);exit;
		$this->display('serve/serve.info.html');
	}
	
	function profile()
	{
		$this->display('member.index.html');
	}
	
	/**
	 * 预约量体
	 */
	function figure()
	{
		if (!IS_POST) 
		{
			//===== 过滤二级地区 =====
			$region_mod  =& m('region');
			$this->assign('regions',$region_mod->getServeRegion());
			/* 导入jQuery的表单验证插件 */
			$this->import_resource(array(
					'script' => 'jquery.plugins/jquery.validate.js'
			));
			$this->display('/serve/paycenter.figure.html');
		}
		else 
		{
// print_exit($_POST);	
			$figure_mode = isset($_POST['figure_mode']) ? intval($_POST['figure_mode']) : '';
			$is_ajax = isset($_POST['is_ajax']) ? $_POST['is_ajax'] : 0;
// echo $figure_mode;exit;
			$data['figure_mode'] = $figure_mode;
			$data['username'] = $_POST['username'];
			$data['mobile'] = $_POST['mobile'];
			$data['address'] = $_POST['address'];
			$data['timeserve'] = $_POST['timeserve'];
			$data['create_date'] = date("Y-m-d H:i:s");
			$data['userid'] = $this->visitor->get('user_id');
			if ($figure_mode == 1) 
			{
				//$data['city3'] = $_POST['city3'];
			}
			elseif ($figure_mode == 2) 
			{
				$data['idserve'] = $_POST['serve_id'];
				//$data['city3_shang'] = $_POST['city3_shang'];
			}
			else 
			{
				$this->show_warning('非法数据');
				return;
			}
			$subscribe_mod = m('subscribe');
			$sub_id = $subscribe_mod->add($data);
			
			if ($is_ajax) 
			{
				$this->json_result(array('id'=>$sub_id));
				return;
			}
			
			$this->show_message('预约成功');
			
			
		}
		
	}
	
	
	/**
	* 获得有服务点的三级地区
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-3-31
	*/
	function getServeRegionThree() 
	{
		$parent_id = isset($_POST['region_id']) ? $_POST['region_id'] : '';
		if (!$parent_id) 
		{
			$this->json_error('error');
			return;
		}
		
		$region_mod = m('region');
		
		$region_list = $region_mod->find(array(
				'conditions' => "parent_id=$parent_id AND is_serve = 1",
		));
		
		$html = "<option value=''>请选择</option>";
		foreach ($region_list as $key => $value)
		{
			$html .= "<option value=".$key.">".$value['region_name']."</option>";
		}
		
		$this->json_result($html);
	}
	
	/**
	* 获得服务点
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-4-1
	*/
	function getServe() 
	{
		$region_id = isset($_POST['region_id']) ? intval($_POST['region_id']) : '';
		if (!$region_id)
		{
			$this->json_error('error');
			return;
		}
		
		$server_list = $this->_serve_mod->find(array(
			'conditions' => "region_id = $region_id",	
		));
		
		$html = "";
		if ($server_list) 
		{
			foreach ($server_list as $key => $value)
			{
				$html .= <<<END
<div class="select">
<input name="text" onclick="selectServe(this)" type="radio" value="{$key}" class="radio" />
<p class="address">{$value['serve_name']}<br><span>{$value['serve_address']}&nbsp;&nbsp;&nbsp;&nbsp;15698150160</span></p>
</div>
END;
			}
		}
	
		$this->json_result($html);
		
		
	}
	
	function map()
	{
		$this->display('serve/serve.map.html');
	}
	
	function comment(){
		//var_dump($this->visitor->get('user_id'));
		$user_id=$this->visitor->get('user_id');
		
		$point=isset($_POST['point'])?$_POST['point']:'';
		$content=isset($_POST['content'])?$_POST['content']:'';
		
		$args = $this->get_params();
    	$serverid = empty($args[0]) ? 0 : intval($args[0]);
		
		//$serverid=19;
		
		
		//$point=2;
		
		
		//$content='dddddddd';
		
		
		
		$returnstr=0;
		if(!$user_id)
		{
			$this->json_error('未登录');
			return ;
		}else 
		{
			$returnstr=1;
		}
		/*
		$subscribe_mod=m('subscribe');
		$subscribe_data=$subscribe_mod->_count(array(
			'conditions' => 'userid ='.$user_id . ' and idserve='.$serverid,    
		));
		$serverate_mod=m('serverate');
		$serverate_count=$serverate_mod->_count(array(
			'conditions' => 'uid ='.$user_id . ' and idserve='.$serverid,    
		));
		*/
		$subscribe_mod=m('subscribe');
		$datas=$subscribe_mod->get(array(
			'fields'=>'subscribe.idsubscribe',
			'conditions' => 'serve_rate.idsubscribe is null and subscribe.userid ='.$user_id . ' and subscribe.idserve='.$serverid,
			'join'=>'has_serve_rate',
		));
		
		$idsubscribe=$datas['idsubscribe'];
		
		//var_dump($idsubscribe);exit;
		if(!$idsubscribe)
		{
			$returnstr=2;
			$this->json_error('只有服务过的用户才能评论');
			return ;
		}
		
		
		if($point==1||$point==2)
		{
			$star=1;
		}elseif($point==3||$point==4)
		{
			$star=2;
		}else 
		{
			$star=3;
		}
		
		
		$res=setComment($user_id, 0, $serverid, 'serve', $content);
		//var_dump($res);
		
		$serve_detail_mod=m('servedetail');
		
		$serverateadd['idserve']=$serverid;
		if($star==1)
		{
			$serverateadd['bad']=1;
			$serve_detail_mod->edit('idserve='.$serverid,'bad=bad+1');
		}
		elseif($star==2)
		{
			$serverateadd['normal']=1;
			$serve_detail_mod->edit('idserve='.$serverid,'normalnum=normalnum+1');
		}
		elseif($star==3)
		{
			$serverateadd['good']=1;
			$serve_detail_mod->edit('idserve='.$serverid,'goodnum=goodnum+1');
		}
		
		$serverateadd['point']=$point;
		$serverateadd['idcomments']=$res;
		$serverateadd['uid']=$user_id;
		$serverateadd['idsubscribe']=$idsubscribe;
		$serverate_mod=m('serverate');
		$serverate_mod->add($serverateadd);

		$this->json_result(array_values(array($returnstr)));
	}
	function comment_list()
	{
		$m = m('comments');
    	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    	
    	$page = $this->_get_page();
    	$this->assign('load', '1');
    	$list = $m->find(array(
    			'conditions' => "comment_id = '{$id}' AND cate = 'serve'",
    			 'limit' => $page['limit'],
            	 'order' => "add_time DESC",
            	 'count' => true,
    	));
    	
    	$serve_rate_mod=m('serverate');
    	
    	
    	foreach($list as $key => $val){
    		$list[$key]['ftime'] = TimeFormat($val['add_time']);
    		$str = preg_replace("/\[em_(\d+)\]/", '<img src="/themes/mall/default/styles/default/images/arclist/$1.gif">' ,$val['content']);
    		$list[$key]['content'] = $str;
    		
    		$point=$serve_rate_mod->get(array(
    		'fields'=>'point',
    		'conditions' => "idcomments = '{$val['id']}' ",
    		));
    		$list[$key]['point'] = $point['point'];
    		//var_dump($point['point']);exit;
    	}
    	
    	$page['item_count'] = $m->getCount();
    	$this->_format_page($page);
    	$this->assign('page_info', $page);
    	$this->assign('list', $list);
    	//print_R($list);exit;
    	$this->assign('lang', $GLOBALS['__ECLANG__']);
    	$content = $this->_view->fetch("serve.comment.html");
    	
    	$this->json_result($content);
	}
}