<?php

class StyleApp extends BackendApp
{
	var $_style_mod;
	var $_customs_mod;
    function __construct()
    {
    	parent::__construct();
    	$this->_style_mod =& m('items');
    	$this->_link_mod =& m('links');
    	$this->_customs_mod=& m('customs');
    }

    function index()
    {
        $title = isset($_GET['title']) ? trim($_GET['title']) : '';
        $conditions = '';
        if($title){
            $conditions .= " AND title LIKE '%".$title."%'";
        }
        
        $this->assign('cats',$cats);
    	$cat =  isset($_GET['cat']) ? trim($_GET['cat']) : '';
    	if($cat){
    	    $conditions .= " AND cat = '{$cat}'";
    	}

        $page = $this->_get_page(20);
        $items = $this->_style_mod->find(array(
            'conditions' => '1=1 AND type="style"' . $conditions,
            'limit' => $page['limit'],
            'order' => "id DESC, sort_order ASC",
            'count' => true,
        ));
		
        $this->assign('items', $items);
        $page['item_count'] = $this->_style_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->assign('title', $title);
        
        
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('style.index.html');
    }
    
    function add()
    {

    	if (!IS_POST)
    	{
    		$this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
	    	
	    	
	    	$template_name = $this->_get_template_name();
	    	$style_name    = $this->_get_style_name();
	    	
	    	
	    	$this->assign('build_editor', $this->_build_editor(array(
	    			'name' => 'content',
	    			'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
	    	)));
	    	$this->display('style.info.html');
    	}
    	else
    	{
    		
    			$data = array(
    				'name' => $_POST['name'],


    				'sn'   => $_POST['sn'],

    				'sort_order' => $_POST['sort_order'],
    				'small_img'  => $_POST["small_img"],
//     				'middle_img' => $_POST["middle_img"],
    				'add_time'   => gmtime(),
    			    'type'    => "style",
    			    'up_time'   => gmtime(),
    			);
    			
    			$id = $this->_style_mod->add($data);
    	
    			
    			if($id){
    				$this->show_message('款式添加成功!',
    					'back_list', 'index.php?app=style');
    			}
    			else
    			{
    				$this->show_message('款式添加失败!',
    					'back_list', 'index.php?app=style');
    			}
    	}
    	
    }
    
    function edit()
    {
    
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	
    	$data     = $this->_style_mod->find($id);
    	if (empty($data))
    	{
    		$this->show_warning('款式不存在！');
    	
    		return;
    	}
    	
    	if (!IS_POST)
    	{
    		$this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
    				'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
	    	$data    =   current($data);
	    	
	    	$template_name = $this->_get_template_name();
	    	$style_name    = $this->_get_style_name();
	    	$this->assign('build_editor', $this->_build_editor(array(
	    			'name' => 'content',
	    			'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
	    	)));
	    	
	    	$this->assign('data',$data);
    		
    		$this->display('style.info.html');
    	}
    	else
    	{
    			$data = array(
    				'name' => $_POST['name'],


    				'sn'   => $_POST['sn'],

    				'sort_order' => $_POST['sort_order'],
    				'small_img'  => $_POST["small_img"],
//     				'middle_img' => $_POST["middle_img"],
    				'add_time'   => gmtime(),
    			    'up_time'   => gmtime(),
    			);
    		 
    		
    		$res = $this->_style_mod->edit($id, $data);

			$this->show_message('款式编辑成功!',
					'back_list', 'index.php?app=style');

    	}
    }

    function drop()
    {
    	
    	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    	
    	if (!$this->_style_mod->drop($id))    //删除
    	{
    		$this->show_warning($this->_style_mod->get_error());
    	
    		return;
    	}
    	else
    	{
    		$this->_link_mod->drop("d_id = '{$id}'");
    		$this->show_message('款式删除成功!',
    					'back_list', 'index.php?app=style');
    	}

    }
    
    function ajax_customs_info(){
        $ids=$_GET['ids'];
        $did = intval($_GET['did']);
        $cData=$this->_customs_mod->findAll("cst_id IN ({$ids})");
        $links = $this->_link_mod->find(array(
        	"conditions" => "d_id='{$did}'",
        ));
        
        $order = array();
       	foreach($links as $key => $val){
       		$order[$val['c_id']] = $val['lorder'];
       	}
        
        foreach($cData as $key => $val){
        	$cData[$key]['order'] = $order[$key];
        }
        $this->assign('goods_list',$cData);
        $this->display('style.item.html');
    }
    
}

?>
