<?php
class My_coinApp extends MemberbaseApp
{
    private $coin_mod;
	function __construct()
    {
    	parent::__construct();
    	$this->coin_mod = &m("cotcoin");
    }
    
    function index(){
            $list = $this->coin_mod->find(array(
                "conditions" => "is_sale = 1",
                'order'      => "add_time DESC",
            ));
            $this->assign("list", $list);
            $this->assign('page_info',$page);
            $this->_config_seo('title', Lang::get('member_center') . ' - 麦富迪币购买');
            $this->display('my_coin.index.html');
    }
}