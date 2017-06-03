<?php

/* 查看手机验证码 */
class SmsRegTmpApp extends BackendApp
{
	var $_user_mod;
	var $_smsRegTmp_mod;
	
	function __construct()
    {
        $this->SmsRegTmpApp();
    }

    function SmsRegTmpApp(){
    	parent::__construct();
    	$this->_user_mod =& m('member');
    	$this->_smsRegTmp_mod =& m('sms_reg_tmp');
    }

	function index(){
		$conditions = $this->_get_query_conditions(array(
            array(
                'field' => $_GET['field_name'],
                'name'  => 'field_value',
                'equal' => 'like',
            ),
        ));
		$page = $this->_get_page(5);
		$code_list = $this->_smsRegTmp_mod->find(array(
            'conditions'=>'1=1'.$conditions,
            'count' => true,
            'limit' => $page['limit'],
            'order' => 'add_time DESC',
        ));
		$page['item_count'] = $this->_smsRegTmp_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('code_list',$code_list);
       	$this->assign('query_fields', array(
    			'phone' => '手机号',
    	));
    	$this->assign('filtered', $conditions? 1 : 0); //是否有查询条件

		$this->display('smsRegTmp.index.html');
	}


}
?>