<?php


class Brokerage_logApp extends MemberbaseApp
{
	var $_brokerage_log_model,$user_id;
    function __construct()
    {
    	parent::__construct();
    	
    	/* 当前用户中心菜单 */
        $this->_curitem('brokerage_log');
    	$this->user_id = $this->visitor->get('user_id');
    	
        $this->_brokerage_log_model=m('brokerage_log');
    }

    
	function index()
    {
    	
    	 
		$conditions = $this->_get_query_conditions(array(
            array(
                'field' => $_GET['field_name'],
                'name'  => 'field_value',
                'equal' => 'like',
            ),
        ));
    	
    	//更新排序
        if (isset($_GET['sort']) && !empty($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'create_date';
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
                $sort  = 'create_date';
                $order = 'desc';
            }
        }
    	//var_dump($this->visitor->get('idserve'));
    	//var_dump($this->user_id);
    	$page = $this->_get_page();
        $brokerage_logs = $this->_brokerage_log_model->find(array(
        	'conditions' => 'userid = '.$this->user_id . $conditions,    
        	'limit' => $page['limit'],
            'count' => true,
        	'order' => "$sort $order",
        ));

        
        
        
        $this->assign('brokerage_logs', $brokerage_logs);
        $page['item_count'] = $this->_brokerage_log_model->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        
        $this->assign('query_fields', array(
            'idserve' => LANG::get('idserve'),
            'userid'     => LANG::get('userid'),
        ));
        $this->assign('sort_options', array(
            'create_date DESC'   => LANG::get('create_date'),
            'idserve DESC' => LANG::get('idserve'),
            'amount DESC'     => LANG::get('amount'),
        ));
        
		$this->display('brokerage_log.index.html');

    }
	function add()
	{
		$userid=1;
		$amount=100.3;

		$this->_brokerage_log_model->add_log($userid,$amount);
	}
    
}

?>
