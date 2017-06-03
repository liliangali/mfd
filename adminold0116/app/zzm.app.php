<?php


/* 地区控制器 */
class ZzmApp  extends BackendApp
{
	function __construct(){
		
		$region_mod = m('region');
        $list1 = $region_mod->get_options(2);
        $this->assign('region1',$list1);
        $this->GenApp();
    }
	function GenApp(){
        parent::__construct();
        $this->_gen_mod    =& m('generalize');
        $this->_genmember_mod    =& m('generalize_member');
        $this->geninvite_mod =& m('memberinvite');
        $this->_region_mod =& m('region');
    }
    
    //列表
    function index(){
    	
    	$pageye = isset($_GET['page']) ? trim($_GET['page']) : '';
    	
         $conditions .= $this->_get_query_conditions(array(
       
            array(
                'field' => 'name',         //可搜索字段title
                'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
                'assoc' => 'AND',           //关系类型,可以是AND, OR
                'name'  => 'zz_name',         //GET的值的访问键名
                'type'  => 'string',        //GET的值的类型
            ),
            
        ));

        $region_name = isset($_REQUEST['region_name']) ? trim($_REQUEST['region_name']) : '';
        if($region_name)
        {

            $region_info = $this->_region_mod->get("region_name like '%{$region_name}%' ");
            if($region_info['parent_id'] ==2)
            {
	            $region_id = $region_info['region_id'];
	            $conditions .= " AND province = '{$region_id}' ";
            }else
            {
            	$region_id = $region_info['region_id'];
            	$conditions .= " AND city = '{$region_id}' ";
            }
            
        }


    	
        
      //更新排序
        if (isset($_GET['sort']) && !empty($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'id';
             $order = 'DESC';
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
                $sort  = 'id';
                $order = 'DESC';
            }
        }
	      $page = $this->_get_page(30);
	      $list = $this->_gen_mod->find(array(
	            'conditions' => '1=1'.$conditions,
	            'limit' => $page['limit'],
	            'order' => " $sort  $order",
	            'count' => true,
	      	
	        ));
	        
          $jx_member = $this->geninvite_mod->find(array(
      			'conditions'    => 'type = 1 ',
          		'join'     =>  'belongs_to_member',
            	'fields'    => 'member.nickname,member.user_name,member.gender,member.reg_time,member.region_id,member.member_lv_id,member_invite.inviter',
            	'count' => true
        	));
       
	      foreach ($jx_member as $k1=>$v1){
	      	
	      $z_id= $this->_genmember_mod->get(array(
	      	 	'conditions'=>'id ='.$v1['inviter'],
	      	 	'fields'    =>'g_id',
	      	 
	      	 ));
	      	 
	       $jx_member[$k1]['zz_id'] = $z_id['g_id'];
	      	
	      }
	     
        	if ($list) 
        	{	
        	$zz_temp = array();
            foreach ($list as $key => $value) 
            {    
              foreach ($jx_member as $key1=>$v1)
              {
              	if($v1['zz_id']==$list[$key]['id'])
              	{   
              		$list[$key]['zz_member'][] =$jx_member[$key1];
              		$list[$key]['zz_num'] =count($list[$key]['zz_member']);
              	 }
              	
              	
              }
              	 $list[$key]['province'] = $this->get_city($value['province']);
                 $list[$key]['city'] = $this->get_city($value['city']);
                 $gen_member = $this->_genmember_mod->find(array(
                 		'conditions'=>' g_id ='.$value['id'],
                 		'fields'    =>'id',
                 		
                 
                 ));
                 $list[$key]['gen_m']= $gen_member;
                 $list[$key]['gen_num']= count($list[$key]['gen_m']);
                       
            }
        }
       
        $region_mod = m('region');
        $list2 = $region_mod->get_options(246);
        $this->assign('region2',$list2); 
 
     
        $this->assign('list', $list);
        $page['item_count'] = $this->_gen_mod->getCount();
        $this->_format_page($page);
     
        $this->assign('page_info', $page);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
        $this->assign('pageye', $pageye);
      
        $this->assign('zz_name', $_GET['zz_name']);
        $this->assign('region_name', $_REQUEST['region_name']);
        $this->display('zctg/zzminfo.index.html');
    }
    
    
	 function add()
    {
        if (!IS_POST)
        {
            $region_mod = m('region');
            $list2 = $region_mod->get_options(246);
            $this->assign('region2',$list2); 
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('state', array(
                '0'   => LANG::get('state_0'),
                '1' => LANG::get('state_1'),
                '2'     => LANG::get('state_2'),
            ));
            $this->assign('acc',LANG::get('add'));     
            $this->display('zctg/zzminfo.add.html');
        }
        else
        {
            $data = array(
             	'name'     =>  $_POST['z_name'],
            	'province' =>$_POST['p_region_id'],
                'city' => $_POST['region_id'],
            );
            $idserve=   $this->_gen_mod->add($data);
            if (!$idserve)
            {
                $this->show_warning($this->_serve->get_error());
                return;
            }
            $this->show_message('添加成功',
                'back_list',    'index.php?app=zzm&act=index',
                'continue_add', 'index.php?app=zzm&amp;act=add&t='.$_GET['t']
            );
            
        }
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
        //print_r($list);exit;
        $this->json_result($list);
         
    }
    
    function get_city($id)
    {
    	$region_mod = m('region');
    	$city_name = $region_mod->get(array(
    		'conditions'=>'region_id ='.$id,
    		'fields'    => 'region_name',
    	
    	));
    	
    	return $city_name['region_name'];
    	
    }

    function drop()
    {

        $id = isset($_GET['id']) ? trim($_GET['id']) : '';

        if (!$id)
        {
            $this->show_warning('没有组织需要删除');
            return;
        }
        $ids = explode(',', $id);
        
       
        if (!$this->_gen_mod->drop($ids))
        {
            $this->show_warning($this->_gen_mod->get_error());

            return;
        }

        $this->show_message('删除组织成功');


    }

    function check_serve_name()
    {
        $zz_name = $_GET['zz_name'];
        $id = $_GET['id'];
        if (!$zz_name) 
        {
            echo 0;
        }
        
        $conditions = "name = '$zz_name' ";
        
     	if ($id) 
        {
            $conditions .= " AND id != $id";
        }
     
        if ($this->_gen_mod->get($conditions)) 
        {
            echo 0;
        }
        else 
        {
            echo 1;
        }
        
    }
    
    function edit()
    {
    	$page = intval($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    	$zz_name = $_REQUEST['zz_name'] ? $_REQUEST['zz_name'] : '';
        $region_name = $_REQUEST['region_name'] ? $_REQUEST['region_name'] : '';
    	$p_region_id = $_REQUEST['p_region_id'] ? $_REQUEST['p_region_id'] : '';
    	$region_id = $_REQUEST['region_id'] ? $_REQUEST['region_id'] : '';
    	$id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    
    	$info = $this->_gen_mod->get('id ='.$id);
    
    	if(!$id){
    		 $this->show_warning('参数错误');
             return;
    	}
    	if(!IS_POST)
    	{
    		
    		if(!$info){
    			$this->show_warning('组织不存在');
             	return;	
    		}
    		 $region_mod = m('region');
    		 $region_id = $info['city'];
    	 	if ($region_id)
            {
                $region_info = $region_mod->get($region_id);
                $this->assign('p_region_id',$region_info['parent_id']);
                $list2 = $region_mod->get_options($region_info['parent_id']);
                $this->assign('region2',$list2);
            }
             $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
             $this->assign('info',$info);
             $this->display('zctg/zzminfo.add.html');
    		
    	}else{
    	     
    		 $data['province'] = $_POST['p_region_id'];
    		 $data['city'] = $_POST['region_id'];
    		 $data['name'] = $_POST['z_name'];
    	  	
    		 $this->_gen_mod->edit($id, $data);
    		 
    		
    		 $this->show_message('修改成功',
                'back_list',    'index.php?app=zzm&act=index&page='.$page.'&zz_name='.$zz_name.'&region_name='.$region_name,
                '再次编辑',   'index.php?app=zzm&amp;act=edit&amp;id=' . $id.'&t='.$_GET['t']
            );
    		
    		
    	}
    	
    	
    	
    	
    }
    
    
  function export (){
    
    	
    	$users =$this->_genmember_mod->findAll(array(
    			"conditions" => "",
    			'fields' => 'user_id,user_name,real_name,nickname,gender,phone_mob,reg_time,serve_type',
    	));
    	foreach ($users as &$row){
    		switch ($row['gender']){
    			case 1:
    				$row['gender'] = '男';
    				break;
    			case 2:
    				$row['gender'] = '女';
    				break;
    			default:
    				$row['gender'] = '未知';
    				break;
    		}
    		
    			switch ($row['serve_type']){
    				case 0:
    					$row['serve_type'] = '消费者';
    					break;
    				case 1:
    					$row['serve_type'] = '创业者';
    					break;
    				default:
    					$row['serve_type'] = '未知';
    					break;
    			}
    			$row['reg_time']=date("Y-m-d H:i:s",$row['reg_time']);
    		}
    	
    	$fields_name = array('ID','会员名','真实姓名','昵称','性别','手机号','注册时间','会员角色');
    	array_unshift($users,$fields_name);
    	$this->export_to_csv($users, 'member', 'gbk');
    }
 
	
	
}

?>

