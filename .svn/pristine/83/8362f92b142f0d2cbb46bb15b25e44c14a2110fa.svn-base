<?php
/**
 * 用户中心 app- 我的需求
 * @author xiao5 <xiao5.china@gmail.com>
 * @version $Id: my_demand.app.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 www.alicaifeng.com
 * @package app
 */
class My_demandApp extends MemberbaseApp{
    var $mod_order_invoice;
    var $mod_order;
	 function __construct()
    {
        $this->My_demandApp();
    }
    
    /**
     * 重写模板文件
     * @return void
     * @access public
     * @version 1.0.0 (2014-12-17)
     * @author Xiao5
     */
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
    }

    function My_demandApp()
    {
        parent::__construct();
		$this->mod_dmd =& m('demand');
		$this->mod_demand_item =& m('demand_item');
    }
    /**
     * 裁缝用户中心
     * @return void
     * @access public
     * @version 1.0.0 (2014-12-15)
     * @author Xiao5
     */
    function index()
    {
        /* 获取订单列表 */
        $this->_get_demands();

        /* 当前用户中心菜单 */
   
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_demand'));

        $this->assign('ac', ACT);
        $this->assign('type', 'user');
        
        //ns add 获取用户信息
        $user = $this->visitor->get();
        $this->assign('user',$user);
        
        /* 头像 add by xiao5 START */
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        
        //获取头像
        $avatar = $objAvatar->avatar_show($this->visitor->get('user_id'),'big');
        $this->assign('avatar', $avatar);
        
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'my_demand');
        
        /* 显示订单列表 */
        $this->display('my_demand.index.html');
    }

    /**
     *  获取需求列表
     * @return void
     * @access public
     * @see register
     * @version 1.0.0 (2014-12-15)
     * @author Xiao5
     */
    function _get_demands()
    {
    	$args = $this->get_params();
    	//指定 分页位置
    	$params = array('args'=>$args,'pagekey'=>1);
        $page = $this->_get_page(10,$params);
        $model_dmd =& m('demand');
        $args = $this->get_params();
         
        $con = array();
        $_GET['type'] = is_null($args[0]) ? 'all' :$args[0] ;
        
        if (is_numeric($args[0]))
        {
            $con = array(
                array(      //按订单状态搜索
                    'field' => 'status',
                    'name'  => 'type',
                )
            );
        }

        $conditions = $this->_get_query_conditions($con);

        /* 查找 */
        $demands = $model_dmd->findAll(array(
            'conditions'    => "user_id=" . $this->visitor->get('user_id') . "{$conditions}",
//             'conditions'    => "user_id=8 {$conditions}",
            'fields'        => 'this.*',
            'count'         => true,
            'limit'         => $page['limit'],
            'order'         => 'add_time DESC',
        ));
        if ($demands)
        {
            foreach($demands as &$row){
                $row['params'] = unserialize($row['params']);
            }
        }
        $page['item_count'] = $model_dmd->getCount();
        $_status = $model_dmd->_status();

        $this->assign('status', $_status);
        $this->assign('type', $_GET['type']);
        $this->assign('type_name', $_GET['type'] == 'all' ? '全部状态' : $_status[$_GET['type']]);
        $this->assign('demands', $demands);
        $this->_format_page($page,10,$params);
        $this->assign('page_info', $page);
    }
    /**
     *  更改需求状态
     * @return void
     * @access public
     * @see register
     * @version 1.0.0 (2014-12-23)
     * @author Xiao5
     */
    function upstatus(){
        if (is_numeric($_POST['id']) && is_numeric($_POST['t']))
        {
        	$info = $this->mod_dmd->get($_POST['id']);
        	
        	$conditions = '';
        	if ($info['status'] == 2 && $_POST['t'] ==0)
        	{
        		//关闭需求
        		$conditions =  array('status'=>"{$_POST['t']}");
        	    
        	}elseif ($info['status'] == 0 && $_POST['t'] ==2)
        	{
        	    //开启需求
        		$conditions =  array('status'=>"{$_POST['t']}");
        		
        	}elseif ($info['status'] == 3 && $_POST['t'] ==2)
        	{
        		//取消选择 - 优先处理demand_offer 取消选择操作
        		$mod_offer =& m('demandoffer');
        		$mod_offer->edit('md_id = '.$_POST['id'] , array('status'=>"3"));
        		$conditions =  array('status'=>"{$_POST['t']}");
        	}
        
        	if (!$conditions)
        	{
        		$this->json_error('更新失败!');
        		return;
        	}
        	
            $rs = $this->mod_dmd->edit($_POST['id'] , $conditions);
            if ($rs)
            {
                $this->json_result(array('rs'=>$rs));
                return;
            }else {
                $this->json_error('更新失败!');
                return;
            }
        }
        $this->json_error('参数有误!');
        return;
    }
   
}

?>
