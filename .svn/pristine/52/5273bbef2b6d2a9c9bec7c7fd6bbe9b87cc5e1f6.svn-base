<?php
/**
 * 用户中心 我的量体信息
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
     */
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
    }

    
    /**
     * 会员中心 我的量体信息
     * @return void
     * @access public
     */
    public function index()
    {
        /* 获取量体信息列表 */
        $this->_get_figures();
        
        
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

        //获取 量体师基础信息
        $ids = array();
        $sids = i_array_column($figures, 'storeid');
        $member_mod  =& m('member');
        $members = $member_mod->findAll(array(
            'conditions'    => db_create_in($sids,'user_id'),
            'fields'        => 'user_id,nickname',
            'count'         => true,
            'limit'         => $page['limit'],
        ));

        //获取当前特殊量体
        $mbt_mod =& m('mtmbodytype');       
        if ($figures)
        {
            foreach($figures as $k=>&$row){
                //$row['user_id'] = $members[$row['storeid']]['user_id'];
                //特殊量体信息
                //肚
                $row['special_du'] = $mbt_mod->get(array('conditions' => 'id='.$figures[$k]['body_type_24']));
                //臀
                $row['special_tun'] = $mbt_mod->get(array('conditions' => 'id='.$figures[$k]['body_type_26']));
                //手臂
                $row['special_sbi'] = $mbt_mod->get(array('conditions' => 'id='.$figures[$k]['body_type_25']));
                //左肩膀
                $row['special_zjian'] = $mbt_mod->get(array('conditions' => 'id='.$figures[$k]['body_type_19']));
                //右肩膀
                $row['special_yjian'] = $mbt_mod->get(array('conditions' => 'id='.$figures[$k]['body_type_20']));

                //风格上衣
                $row['special_syi'] = $mbt_mod->get(array('conditions' => 'id='.$figures[$k]['body_type_3']));
                //风格西裤
                $row['special_xku'] = $mbt_mod->get(array('conditions' => 'id='.$figures[$k]['body_type_2000']));


                //获取量体师的名字
                $row['nickname'] = $members[$row['storeid']]['nickname'];
            }
        }
        $page['item_count'] = $this->mod_figure->getCount();
        $this->assign('figures', $figures);
        $this->assign('positions', $this->mod_figure->_positions());
        $this->_format_page($page);
        $this->assign('page_info', $page);
    }
    
    /**
     *  撤销量体信息
     * @return void
     * @access public
     */
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