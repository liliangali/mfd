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
        $this->assign('title',"RCTAILOR提供全国免费量体服务、预约量体、上门量体、量体定制-定制是一种生活态度(rctailor.com)");
        //ns add 
        $this->assign('keywords','上门量体、预约量体、免费量体、西服定制量体');
        $this->assign('description','RCTAILOR-定制是一种生活态度(rctailor.com)-红领裁缝，全国上千家店面或合作商，提供免费预约量体，上门量体等服务，让西服定制随心所欲！');
		
        if($_SESSION['user_info']['user_id'])
        {
        	$this->assign('session_info_user_id',$_SESSION['user_info']['user_id']);
        	
        	$this->_subscribe_mod = m('subscribe');
        	$res=$this->_subscribe_mod->get(array(
    			'conditions' => ' (state=0 or state=3) and subscribe_type = 1 and userid = '.$_SESSION['user_info']['user_id'],));
        	if($res)
        	{
        		$this->assign('issubscribe',1);
        		$this->assign('idserve',$res['idserve']);
        		
        	}
        }
        if($_GET['ajax']=='1')
		{
			return ;
		}else 
		{
			//var_dump($_COOKIE);exit;
			$this->display('serve/serve.search.html');
			//$this->display('serve/serve.search-yiqian.html');
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
		
    	$args = $this->get_params();
    	//var_dump($args);
    	//var_dump(count($args));
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
	    			//echo '3';
    			}elseif($args[2])
    			{
    				$region_data=$region_mod->get_descendant(intval($args[2]));
	    			$this->assign('r2', intval($args[2]));
	    			$this->assign('r1', intval($args[1]));
	    			//echo '2';
    			}elseif($args[1])
    			{
    				$region_data=$region_mod->get_descendant(intval($args[1]));
    				$this->assign('r1', intval($args[1]));
    				//echo '1';
    			}
    			
    			
    			
    		}
    		
    		if($region_data)
    		{
    			$conditions=" and region_id in(".implode(',',$region_data).")";

    		}
    		
    		
    		
    		
    		//var_dump($conditions);
    	}elseif($_COOKIE['r1']&&$_COOKIE['r2']&&$_COOKIE['value'])
    	{
    		//var_dump($_COOKIE['r1']);exit;
    		$this->assign('r1', $_COOKIE['r1']);
    		$this->assign('r2', $_COOKIE['r2']);
    		$this->assign('value', $_COOKIE['value']);
    		
    	}
    	
    	/*
		$conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'region_id',//字段
                'name'  => 'value',//get value
                'equal' => 'in',
            ),
        ));
        */
    	//var_dump($conditions);exit;
    	/*
        
        */
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
        	//echo $page['limit'];exit;
        }else 
        {
        	$page = $this->_get_page(5);
        	//echo '5';exit;
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

        //var_dump($page);
        
        $this->assign('page_info', $page);
        
		/* 导入jQuery的表单验证插件 */
		$this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.ad.js,mlselection.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
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
			
			$serverate_data=$serverate_mod->getrow('SELECT AVG(point) point,count(1) cot,(sum(point)/(count(1)*5))*100 coa  FROM cf_serve_rate where idserve='.$args[0]);
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
    	
		
		//var_dump(serve_rate_mod);
    	
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
	
	
	function contactus()
	{
		
		if(!IS_POST)
		{
	
			//var_dump($this->mcityname);exit;
			
			
			$pageindex=isset($_GET["mp"])?$_GET["mp"]:1;
			
			
			
			$res=$this->getshoplist(0,$pageindex);
	
			//var_dump($res);
			$this->assign('goods_list',$res);
			if(isset($_GET["ajax"])&&$_GET["ajax"]=="1")
			{
				//var_dump($res);exit;
				
				if($res)
				{
					$result = $this->_view->fetch('index_contactus_list.html');
					$this->json_result($result);
				}else{
					$this->json_error("error");
				}
			}else 
			{
				$this->display('index_contactus.html');
			}
			
			
			
			
		}
	
			
	}
	
	function getshoplist($type=0,$pageindex=1)
	{
		$testsite_mod=m('testsite');
			
			
		$typestr=$type==0?'':' and (type = '.$type.' or type =0 ) ';
			
		$citystr=$this->mcityname==''?'':'  and city_name=\''.$this->mcityname.'\' ';
			
			
		$res=$testsite_mod->find(array(
				'conditions'=>' 1=1 '.$typestr.$citystr,
				'limit'=> (10 * ($pageindex-1)) . ','. 10,
		));
			
			
			
			
			
		return $res;
	}
}