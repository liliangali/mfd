<?php


class BrokerageApp extends BackendApp
{
	var $_brokerage_model,$serve_type;
    function __construct()
    {
    	parent::__construct();
        $this->_brokerage_model=m('brokerage');
    	if($_GET['t']=='2')
        {
        	$this->serve_type=3;
        }else {
        	$this->serve_type=2;
        }
    }

	function index()
    {
       
        //var_dump($this->serve_type);
    	$page = $this->_get_page();
        $brokerages = $this->_brokerage_model->find(array(
        	'conditions' => 'serve_type='.$this->serve_type . $conditions,    
        	'limit' => $page['limit'],
            'count' => true,
        ));

        $this->assign('brokerages', $brokerages);
        $page['item_count'] = $this->_brokerage_model->getCount();
        $this->_format_page($page);

        $this->assign('page_info', $page);
		$this->display('brokerage.index.html');
        
    }
    
	function edit()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 是否存在 */
            $brokerage = $this->_brokerage_model->get_info($id);
            if (!$brokerage)
            {
                $this->show_warning('user_empty');
                return;
            }

            
            $this->assign('brokerage', $brokerage);

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            
        	$this->assign('acc',LANG::get('edit'));
            $this->display('brokerage.form.html');
        }
        else
        {
            $data = array(
                'serve' => $_POST['serve'],
                
            );
            
            

            

            /* 修改本地数据 */
            $this->_brokerage_model->edit($id, $data);

            

            $this->show_message('修改成功',
                'back_list',    'index.php?app=brokerage&t='.$_GET['t'],
                'edit_again',   'index.php?app=brokerage&amp;act=edit&amp;id=' . $id.'&t='.$_GET['t']
            );
        }
    }
}

?>
