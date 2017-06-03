<?php
/**
 * 用户中心 app- 我的量体信息
 * @author xiao5 <xiao5.china@gmail.com>
 * @version $Id: my_body.app.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 www.alicaifeng.com
 * @package app
 */
class My_bodyApp extends MemberbaseApp
{
	function __construct()
    {
    	parent::__construct();
    	
    	Lang::load(lang_file('subscribeinfo'));
    	
    	$this->mod_figure=m('member_figure');
    	$this->user_id = $this->visitor->get('user_id');
    }
    /**
     * 重写模板文件
     * @return void
     * @access public
     * @version 1.0.0 (2014-11-17)
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

    
    /**
     * 会员中心 我的量体信息
     * @return void
     * @access public
     * @version 1.0.0 (2014-12-15)
     * @author Xiao5
     */
    public function index()
    {
        /* 获取量体信息列表 */
        $this->_get_figures();
    
        /* 当前用户中心菜单 */
         
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_body'));
    
        $this->assign('ac', ACT);
        $this->assign('type', 'user');

        //ns add 获取用户信息
        $user = $this->visitor->get();
        $this->assign('user',$user);
        
        /* 头像 add by xiao5 START */
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        
        //获取头像
        $avatar = $objAvatar->avatar_show($this->user_id,'big');
        $this->assign('avatar', $avatar);
        
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'my_body');
    
        /* 显示订单列表 */
        $this->display('my_body.index.html');
    }
    
    /**
     *  获取量体信息列表
     * @return void
     * @access public
     * @version 1.0.0 (2014-12-15)
     * @author Xiao5
     */
    function _get_figures()
    {
    
        $page = $this->_get_page(10);
        $args = $this->get_params();
         
        $conditions = '';
         
        /* 查找 */
        $figures = $this->mod_figure->findAll(array(
            'conditions'    => "userid=" . $this->visitor->get('user_id') . "{$conditions}",
            'fields'        => 'this.*',
            'count'         => true,
            'limit'         => $page['limit'],
            'order'         => 'lasttime DESC',
        ));
         
        #----！切记不要链表！-----#
    
        //获取 裁缝的基础信息
        $ids = array();
        $sids = i_array_column($figures, 'storeid');
        $store_mod  =& m('store');
        $stores = $store_mod->findAll(array(
            'conditions' => db_create_in($sids, 'store_id'),
            'fields' => 'popularity,published,fans,owner_name,store_logo,sgrade', // 人气,作品,粉丝,昵称,logo图,等级
            'count' => true,
            'limit' => $page['limit']
        ));
            
        
        // 获取 裁缝等级的logo 数组关联，不链表
        $member_lv_mod = & m('memberlv');
        $sgradeids = i_array_column($stores, 'sgrade');
        $lvs = $member_lv_mod->findAll(array(
            'conditions' => db_create_in($sgradeids, 'member_lv_id'),
            'fields' => 'name,lv_logo', // 等级名称 ，等级logo
            'count' => true,
            'limit' => $page['limit'],
        ));
        if ($figures) {
            foreach ($figures as &$row) {
                $row['popularity'] = $stores[$row['storeid']]['popularity'];
                $row['published'] = $stores[$row['storeid']]['published'];
                $row['fans'] = $stores[$row['storeid']]['fans'];
                $row['owner_name'] = $stores[$row['storeid']]['owner_name'];
                $row['store_id'] = $stores[$row['storeid']]['store_id'];
                $row['store_logo'] = site_url() . $stores[$row['storeid']]['store_logo'];
                $row['sgrade'] = $stores[$row['storeid']]['sgrade'];
                $row['lv_logo'] = ''; // 默认等级logo
                $row['lv_name'] = '';
                if ($lvs[$stores[$row['storeid']]['sgrade']]) {
                    $row['lv_logo'] = site_url() . $lvs[$stores[$row['storeid']]['sgrade']]['lv_logo'];
                    $row['lv_name'] = $lvs[$stores[$row['storeid']]['sgrade']]['name'];
                }
            }
    }

        $page['item_count'] = $this->mod_figure->getCount();
        $this->assign('figures', $figures);
        $this->assign('positions', $this->mod_figure->_positions());
        $this->_format_page($page);
        $this->assign('page_info', $page);
    }
    
    function userexit()
    {
    	$args = $this->get_params();
    	
    	$id = empty($args[0]) ? 0 : intval($args[0]);
        //$id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_user_to_drop');
            return;
        }
        
		
        
		$arrsubscribe=array();
        $arrsubscribe['state']='2';
        
        
        if (!$this->_subscribe->edit('idsubscribe='.$id.' and userid='.$this->user_id.' and state=0 ',$arrsubscribe))
        {
            $this->show_warning('撤销失败');

            return;
        }

        $this->show_message('撤销成功');
    }
    
    
}