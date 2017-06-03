<?php


/* 地区控制器 */
class JxApp  extends BackendApp
{
	function __construct(){
        $this->JxApp();
    }
	function JxApp(){
        parent::__construct();
        $this->geninvite_mod =& m('memberinvite');
        $this->_member_mod =& m('member');
        $this->_memberlv_mod =& m('memberlv');
        $this->_generalizemem_mod =& m('generalize_member');
        $this->_region_mod =& m('region');
        $this->_special_code_mod = & m('special_code');
    }

   /*       array(
                'field' => $_GET['field_name'],
                'name'  => 'field_value',
                'equal' => 'LIKE',
            ),
    */
    //列表
    function index(){
    
       $pageye = isset($_GET['page']) ? trim($_GET['page']) : '';
        $conditions = '';
    	 $conditions .= $this->_get_query_conditions(array(
      
            array(
                'field' => 'gender',
           		'name' =>  'gender',
                'equal' => '=',
                'type'  => 'numeric',
            ),
             array(
                'field' => 'member_lv_id',
                'equal' => '=',
                'type'  => 'numeric',
            ),
             array(
                 'field' => 'member_invite.add_time',
                 'name'  => 'add_time_from',
                 'equal' => '>=',
                 'handler'=> 'gmstr2time',
             ),array(
                 'field' => 'member_invite.add_time',
                 'name'  => 'add_time_to',
                 'equal' => '<=',
                 'handler'   => 'gmstr2time_end',
             )

        ));

        $field_name = isset($_REQUEST['field_name']) ? trim($_REQUEST['field_name']) : '';
        $field_value = isset($_REQUEST['field_value']) ? trim($_REQUEST['field_value']) : '';
        if($field_name =='member_invite.inviter' && isset($field_value)){
            $info = $this->_generalizemem_mod->get("phone ='$field_value'");
            $id = $info['id'];
            $conditions .= " AND member_invite.inviter = '{$id}' ";
            
        }
        if($field_name =='member.user_name' && isset($field_value)){

            $conditions .= " AND member.user_name  like '%{$field_value}%'";
        }

        if($field_name =='member.region' && isset($field_value)){

          $region_info = $this->_region_mod->get("region_name like '%{$field_value}%' ");
          $region_id = $region_info['region_id'];
          $conditions .= " AND member.region_id = '{$region_id}' ";

        }

        
      //更新排序
        if (isset($_GET['sort']) && !empty($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'user_id';
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
                $sort  = 'user_id';
                $order = 'DESC';
            }
        }

    	  $sex = array(1=>'男',2=>'女');
	      $page = $this->_get_page(20);


        $list = $this->_member_mod->find(array(
            'join'          => 'has_member',//,has_special
            'conditions'    =>' member_invite.type=1'.$conditions,
            'limit'         => $page['limit'],
            'fields'        => 'member.user_id,member.nickname,member.user_name,member.gender,member.reg_time,member.region_id,member.member_lv_id,member_invite.inviter,member_invite.invitee,member_invite.id,member_invite.add_time',//special_code.to_time, special_code.cate,
            'order'         => "$sort $order",
            'count'         => true
        ));
//echo '<pre>';var_dump($list);die;


       $order_mod =& m('order');
       foreach ($list as $key=>$val){
            $list[$key]['gender'] = $sex[$val['gender']];
            $list[$key]['reg_time'] = date("Y-m-d H:i:s",$val['reg_time']);
            $list[$key]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
//	       		$list[$key]['to_time'] = !$val['to_time']?'×':local_date('Y-m-d H:i:s',$val['to_time']);

            if($val['member_lv_id']){

            $list[$key]['member_lv_id'] =$this->get_memlv($val['member_lv_id']);
            }

            if($val['region_id']){
            $list[$key]['region_id'] = $this->get_city($val['region_id']);

            }

            if($val['inviter']){
                $tgy = $this->_generalizemem_mod->get("id = ".$val['inviter']);
                $list[$key]['name'] = $tgy['phone'];
                $list[$key]['inviter'] = $tgy['name'];
            }

           $order_num  = $order_mod->get(array(
               'conditions'    =>"user_id=".$key." AND status = 40 AND add_time>".$val['add_time'],//AND add_time>绑定事件
               'fields'        =>'add_time,count(*) as order_num'
           ));
           $list[$key]['order_num'] =empty($order_num)?0:$order_num['order_num'];
       }


        $this->assign('list', $list);
        $page['item_count'] = $this->_member_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $this->assign('query_fields', array(
        	'member.user_name'      => '创业者帐号',
            'member_invite.inviter' => '推广员帐号',
            'member.region'         =>'所属地区',
         
        ));


        $this->assign('c_arr', array(1=>'越级码',2=>'初级码'));
        $this->assign('sex_option', $sex);
        $this->assign('pageye', $pageye);
        $this->assign('fn', $field_name);
        $this->assign('fv', $field_value);
        $this->assign('ml', $_REQUEST['member_lv_id']);
        $this->assign('g', $_REQUEST['gender']);
        $this->assign('t', $_REQUEST['add_time_to']);
        $this->assign('f', $_REQUEST['add_time_from']);
        $this->display('zctg/jxinfo.index.html');
    }
    
    
    function view(){
    	
      $id = $_GET['id'];
      $page = intval($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
      $field_name = isset($_REQUEST['field_name']) ? $_REQUEST['field_name'] : '';
      $field_value = isset($_REQUEST['field_value']) ? $_REQUEST['field_value'] : '';
      $member_lv_id = isset($_REQUEST['member_lv_id']) ? $_REQUEST['member_lv_id'] : '';
      $gender = isset($_REQUEST['gender']) ? $_REQUEST['gender'] : '';
      $add_time_to = isset($_REQUEST['add_time_to']) ? $_REQUEST['add_time_to'] : '';
      $add_time_from = isset($_REQUEST['add_time_from']) ? $_REQUEST['add_time_from'] : '';
    	$list = $this->geninvite_mod->get(array(
	      		'conditions'    => 'id ='.$id,
	          	'join'     =>  'belongs_to_member',
                'fields'    => 'member.user_id,member.nickname,member.user_name,member.gender,member.reg_time,member.region_id,member.member_lv_id,member.email,member.birthday,member.im_qq,member_invite.inviter,member.region_name',
                'count' => true
	        ));

	    $sex = array(1=>'男',2=>'女');
    	$list['gender'] = $list['gender']?$sex[$list['gender']]:"";
       	$list['reg_time'] = date("Y-m-d H:i:s",$list['reg_time']);
       	if($list['member_lv_id']){
       		
           $list['member_lv_id'] =$this->get_memlv($list['member_lv_id']);
       	}

       	if($list['inviter']){
       	$tgy = $this->_generalizemem_mod->get("id = ".$list['inviter']);
       	$list['inviter'] = $tgy['name'];
       	$list['phone'] = $tgy['phone'];
       	}

       $auth_mod = &m('auth');
       $auth_info = $auth_mod->get("user_id='{$list['user_id']}'");
       if($auth_info && $auth_info['province'] && $auth_info['city']){
           $region_mod = &m('region');
           $region_info = $region_mod->find(db_create_in(array($auth_info['province'],$auth_info['city']), 'region_id'));
           $list['region_name']=$region_info[$auth_info['province']]['region_name'].','.$region_info[$auth_info['city']]['region_name'];
       }

      $this->assign('list', $list);
      $this->assign('page', $page);
      $this->assign('field_name', $field_name);
      $this->assign('field_value', $field_value);
      $this->assign('gender', $gender);
      $this->assign('member_lv_id', $member_lv_id);
      $this->assign('add_time_to', $add_time_to);
      $this->assign('add_time_from', $add_time_from);
      $this->display('zctg/jxinfo.form.html');
    
    	
    }
    
 
	    // 查看会员等级
	    
	    function get_memlv($id){
	    	
	    	$memlv=  $this->_memberlv_mod->get("member_lv_id =$id");
	    	return $memlv['name'];
	    	
	    	
	    }
    
        function get_city($id){
    	
    	$region_mod = m('region');
    	$city_name = $region_mod->get(array(
    		'conditions'=>'region_id ='.$id,
    		'fields'    => 'region_name',
    	
    	));
    	
    	return $city_name['region_name'];
    	
    	
    	
    }

    /*
     * 单个解绑
     * 2015-08-06 yusw
     * */
    function drop(){
        //手动删除 ，不删除，创业者和消费者之间关系  等级全部清理为1
        //自动删除   删除   创业者和消费者之间关系   等级依旧特权
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $data_log = array();
      
        if (!$id)
        {
            $this->show_warning('绩效记录不存在');
            return;
        }

//        $this->_special_code_mod = & m('special_code');
//        $this->_special_log_mod = & m('special_log');


        $lists = $this->geninvite_mod->get('type=1 and id='.$id);
//        $log_lists  = $this->_special_code_mod->get('is_used=1 and to_id='.$lists['invitee']);
//
//        //清理 关系
//        $d_special_num  = $this->_special_code_mod->drop('is_used=1 and to_id='.$lists['invitee']);//影响行数
//        if(!empty($log_lists)){
//            $this->_special_log_mod->setDec('id='.$log_lists['log_id'],'num',$d_special_num);
//            $data_log['special_code'] = json_encode($log_lists);
//        }

        $m = &m('member');
        $user_info  = $m->get($lists['invitee']);
        $data_log['old_member_lv_id']=$user_info['member_lv_id'];

        //降级
        if($user_info['member_lv_id'] <5){
            $this->_member_mod->edit('user_id='.$lists['invitee'],array('member_lv_id'=>'1'));
        }
        $d_invite = $this->geninvite_mod->drop('type=1 and id='.$id);
        if(!$d_invite){
            $this->show_warning("系统异常", 'back_list', 'index.php?app=jx&amp;act=index');
            return false;
        }

        //日志
        $jb_log = & m('cy_reload_log');
        $data_log['user_id'] = $lists['invitee'];
        !empty($lists) && $data_log['invitee'] = json_encode($lists);//inviter是 清理 创业者和用户的关系  后台暂时不做处理
        $data_log['add_time'] = time();
        $data_log['member_lv_id'] = 1;
        $data_log['type']=1;


        $jb_log->add($data_log);
        $this->show_message('解绑成功');

    }
    
    
     /**
     * 导出excel
     * @author sauren  <6582701@qq.com>
     * @2015-6-13
     * 本方法用于将会员列表内所有的信息生成excel表
     *
     */
    function export (){
    

  
      
     $list = $this->_member_mod->find(array(
            'join'          => 'has_member',//,has_special
            'conditions'    =>' member_invite.type=1',
            'fields'        => 'member.user_id,member.nickname,member.user_name,member.gender,member.reg_time,member.region_id,member.member_lv_id,member_invite.inviter,member_invite.invitee,member_invite.id,member_invite.add_time',//special_code.to_time, special_code.cate,
            'order'         =>'member.user_id DESC',
            'count'         => true
        ));
     
  	  $lists = array();
  	  $order_mod =& m('order');
      foreach ($list as  $key=>&$row)
      {
      	 $lists[$key]['id'] = $row['user_id'];
      	 $lists[$key]['nickname'] = $row['nickname'];
      	 $lists[$key]['user_name'] = $row['user_name'];
         // 下单数量
         $order_num  = $order_mod->get(array(
               'conditions'    =>"user_id=".$key." AND status = 40 AND add_time>".$row['add_time'],//AND add_time>绑定事件
               'fields'        =>'add_time,count(*) as order_num'
           ));
        $lists[$key]['order_num'] =empty($order_num)?0:$order_num['order_num'];
        
         $lists[$key]['reg_time'] = date("Y-m-d H:i:s",$row['reg_time']);
         $lists[$key]['add_time'] = date("Y-m-d H:i:s",$row['add_time']);
      

         if($row['member_lv_id'])
         {
            $lists[$key]['member_lv_id'] = $this->get_memlv($row['member_lv_id']);
         }else
         {
         	
         	$lists[$key]['member_lv_id'] = $row['member_lv_id'];
         }
         
        if($row['inviter'])
        {
          $tgy = $this->_generalizemem_mod->get("id = ".$row['inviter']);
          $lists[$key]['inviter'] = $tgy['name'];
          $lists[$key]['phone'] = $tgy['phone'];
        }
      
          

    }
     
      $fields_name = array('ID','会员昵称','会员名称','下单数','注册时间','激活BD码时间','当前等级','推广员名称','推广员手机');
      array_unshift($lists,$fields_name);
      $this->export_to_csv($lists, 'lists', 'gbk');
    }	
}
?>