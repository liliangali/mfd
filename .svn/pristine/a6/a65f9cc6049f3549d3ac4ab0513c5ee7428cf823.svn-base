<?php


class FigureadminlogApp extends MemberbaseApp
{
	var $_figureadminlog,$user_id;
    function __construct()
    {
    	parent::__construct();
        $this->_figureadminlog=m('figureadminlog');
        
        /* 当前用户中心菜单 */
        $this->_curitem('figureadminlog');
    	$this->user_id = $this->visitor->get('user_id');
    	$this->idserve = $this->visitor->get('idserve');
    	//var_dump($this->idserve);exit;
    }

    function index()
    {
    	
    	
    	
        $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
        
    	//更新排序
        if (isset($_GET['sort']) && !empty($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'idfigureadminlog';
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
                $sort  = 'idfigureadminlog';
                $order = 'desc';
            }
        }
        
        $page = $this->_get_page();
        $figures = $this->_figureadminlog->find(array(
        	'conditions' => 'idserve ='.$this->idserve . $conditions,    
        	'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));

        $this->assign('datas', $figures);
        $page['item_count'] = $this->_figureadminlog->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        
        $this->display('figureadminlog/figureadminlog.index.html');
    }
	
    
	
    
	
    
}

?>
