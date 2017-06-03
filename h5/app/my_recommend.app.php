<?php
class My_recommendApp extends MemberbaseApp
{
	function __construct()
    {
    	parent::__construct();
    	
        $this->_curitem('my_recommend');
        $this->_curmenu('my_recommend');
    }
    function index()
    {
    	$this->assign('title', 'RCTAILOR-酷客中心-我的推荐');
        $this->assign('title_info', '我的推荐');
        $this->display('my_recommend.index.html');
    }
	function _get_member_submenu()
    {
        $menus = array(
            array(
                'name'  => 'my_recommend',
            ),
        );
        return $menus;
    }
}