<?php
/**
 * 用户中心 app- 我的粉丝
 * @author xiao5 <xiao5.china@gmail.com>
 * @version $Id: my_fans.app.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 www.alicaifeng.com
 * @package app
 */
class My_fansApp extends MemberbaseApp{
    var $mod_order_invoice;
    var $mod_order;
    var $objAvatar;
	 function __construct()
    {
        $this->My_fansApp();
    }
    
    /**
     * 重写模板文件
     * @return void
     * @access public
     * @see _config_view
     * @version 1.0.0 (2014-12-17)
     * @author Xiao5
     */
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
        $this->_view->template_dir = ROOT_PATH . "/view/".LANG."/mall/{$template_name}/user_center";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}/user_center";
        $this->_view->res_base     = SITE_URL . "/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }

    function My_fansApp()
    {
        parent::__construct();
		$this->mod_follow =& m('userfollow');
		/* 头像 add by xiao5 START */
		require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
		$this->objAvatar = new Avatar();
    }
    /**
     * 裁缝中心 我的关注
     * @return void
     * @access public
     * @version 1.0.0 (2014-12-15)
     * @author Xiao5
     */
    public function tailor()
    {
        /* 获取订单列表 */
        $this->_get_fans();

        /* 当前用户中心菜单 */
   
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_demand'));

        $this->assign('ac', ACT);
        
        
        $this->assign('type', 'user');
        
       
        
        //获取头像
        $user = $this->visitor->get();
        $this->assign('user',$user);
        $avatar = $this->objAvatar->avatar_show($user['user_id'],'big');
        $this->assign('avatar', $avatar);
        
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'my_fans');
        
        /* 显示订单列表 */
        $this->display('my_fans.index.html');
    }

    /**
     *  获取关注列表
     * @return void
     * @access public
     * @version 1.0.0 (2014-12-15)
     * @author Xiao5
     */
    function _get_fans()
    {
        $page = $this->_get_page(10);
        $args = $this->get_params();
         
        $conditions = '';
   
        /* 查找 */
        $fans = $this->mod_follow->findAll(array(
            'conditions'    => "follow_uid=" . $this->visitor->get('user_id') . "{$conditions}",
            'fields'        => 'this.*',
            'count'         => true,
            'limit'         => $page['limit'],
            'order'         => 'add_time DESC',
        ));
       
        #----！切记不要链表！-----#
        
        //获取 裁缝的基础信息
        $ids = array();
        $sids = i_array_column($fans, 'uid');
        $member_mod  =& m('member');
        $members = $member_mod->findAll(array(
            'conditions'    => db_create_in($sids,'user_id'),
            'fields'        => 'user_id,nickname,single,demand,member_lv_id',//uid,昵称,晒单数，需求数，等级
            'count'         => true,
            'limit'         => $page['limit'],
        ));
        
        
        //获取 裁缝等级的logo 数组关联，不链表
        $member_lv_mod  =& m('memberlv');
        $sgradeids = i_array_column($members, 'member_lv_id');
        $lvs = $member_lv_mod->findAll(array(
            'conditions'    => db_create_in($sgradeids,'member_lv_id'),
            'fields'        => 'name,lv_logo',//等级名称 ，等级logo
            'count'         => true,
            'limit'         => $page['limit'],
        ));
       
        if ($fans)
        {
            foreach($fans as &$row){
                $row['user_id'] = $members[$row['uid']]['user_id'];
                $row['nickname'] = $members[$row['uid']]['nickname'];
                $row['single'] = $members[$row['uid']]['single'];
                $row['demand'] = $members[$row['uid']]['demand'];
                $row['avatar'] = $this->objAvatar->avatar_show_src($row['uid'],'big');
                $row['lv_logo'] = '';//默认等级logo
                $row['lv_name'] = '';
                if($lvs[$members[$row['uid']]['member_lv_id']])
                {
                    $row['lv_logo'] = site_url().$lvs[$members[$row['uid']]['member_lv_id']]['lv_logo'];
                    $row['lv_name'] = $lvs[$members[$row['uid']]['member_lv_id']]['name'];
                }
               
            }
        }


        $page['item_count'] = $this->mod_follow->getCount();
        $this->assign('fans', $fans);
        $this->_format_page($page);
        $this->assign('page_info', $page);
    }

}

?>
