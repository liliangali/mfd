<?php
/**
 *    印花管理控制器
 *    @author shaozizhen
 */

class Yh_templateApp extends BackendApp
{
    var $_member_mod;
	var $_template_mod;
	var $category_mod;
    function __construct()
    {
    	define("YH", "yh_template/");
        $this->Yh_templateApp();
    }
    function Yh_templateApp()
    {
        parent::BackendApp();
        $this->_member_mod =&m('member');
        $this->_template_mod =&m('yh_template');
        //===== assign分类数据 =====
       $this->category_mod =  &m('YhCategory');
       $this->assign('cats',$this->category_mod->find());
        
    }
 
    /**
     * 印花管理列表
     */
    function index()
    {

	     $title = isset($_GET['title']) ? trim($_GET['title']) : '';
        $conditions = '';
        if($title){
            $conditions .= " AND title LIKE '%".$title."%'";
        }
		
		 $cat =  isset($_GET['cat']) ? trim($_GET['cat']) : '';
    	if($cat){
    	    $conditions .= " AND category = '{$cat}'";
    	}
		
    	$conditions .= "AND source=1 ";
        $page = $this->_get_page(30);
        $items = $this->_template_mod->find(array(
            'conditions' => '1=1 ' . $conditions,
            'limit' => $page['limit'],
            'order' => " sort_order ASC,id DESC",
            'count' => true,
        ));
        
        $page['item_count'] = $this->_template_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
		
		$cates=$this->category_mod->find();
		foreach($items as $key=>$row){
			$items[$key][name] = $cates[$row['category']]['name'];
		}
		$this->assign('items', $items);

        $this->assign('title', $title);
        
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display(YH."yh_template.index.html");
    }
    
    
    function custom(){
    	
    	$conditions .= "AND source=2 ";
    	$page = $this->_get_page(20);
    	$items = $this->_template_mod->find(array(
    			'conditions' => '1=1 ' . $conditions,
    			'limit' => $page['limit'],
    			'order' => "id DESC, sort_order ASC",
    			'count' => true,
    	));
    	$username=$this->_member_mod->find();
    	foreach($items as $key=>$row){
    		$items[$key][username] = $username[$row['uid']]['nickname'];
    	}
    	$page['item_count'] = $this->_template_mod->getCount();
    	$this->assign('items', $items);
    	$this->import_resource(array(
    			'script' => 'jqtreetable.js,inline_edit_admin.js',
    			'style'  => 'res:style/jqtreetable.css'
    	));
    	
    	
    	

    	$this->display(YH."yh_template.custom.html");
    }
    
    
    function add()
    {

    	if (!IS_POST)
    	{
    		$this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
	    	
	    	
	    	$template_name = $this->_get_template_name();
	    	$style_name    = $this->_get_style_name();
	    	
	    	//$this->assign("cats", $this->disCats());
	    	
	    	$this->assign('build_editor', $this->_build_editor(array(
	    			'name' => 'content',
	    			'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
	    	)));
	    	$this->display(YH."yh_template.info.html");
    	}

    		if(IS_POST)
    		{	$data = array(
    				'title' => $_POST['title'],
    				'price' => $_POST['price'],
    				'category' => $_POST['cat'],//分类
    				'images'  => $_POST["small_img"],
    				'state'   => $_POST['is_hot'],
 					'sort_order' => $_POST['sort_order']
    			);
    	
    			$id = $this->_template_mod->add($data);
    			
    			
    			if($id){
    			
    				
  				$this->show_message('印花添加成功!',
     					'back_list', 'index.php?app=yh_template');
    			}
    			else
    			{
    			$this->show_message('印花添加失败!',
    					'back_list', 'index.php?app=yh_template');
    			}
    		}
    	
    	
    }
    
    function edit()
    {
    
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	
    	$data     = $this->_template_mod->find($id);
    	if (empty($data))
    	{
    		$this->show_warning('印花不存在！');
    	
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
	    	
	    	if(isset($link) && !empty($link)){
	    	    $linkid=implode(',', $link);
	    	}else{
	    		$linkid='';
	    	}
	    	
	    	$this->assign('linkid',$linkid);
	    	
	    	//$this->assign("cats", $this->disCats());
	    	
	    	$this->assign('data',$data);
    		
    		$this->display(YH."yh_template.info.html");
    	}
    	else
    	{
	
    		$data = array(
    				'title' => $_POST['title'],
    				'price' => $_POST['price'],
    				'category' => $_POST['cat'],//分类
    				'sort_order' => $_POST['sort_order']
    		);
    		if ( $_POST["small_img"]) 
    		{
    			$data['images'] = $_POST['small_img'];
    		}
    	}
    	if (IS_POST){
    		$res = $this->_template_mod->edit($id, $data);
    		
    		
			if($res){
			$this->show_message('印花编辑成功!',
					'back_list', 'index.php?app=yh_template');
            }
    	}
    }

    function drop()
    {
    	
    	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    	
    	if (!$this->_template_mod->drop($id))    //删除
    	{
    		$this->show_warning($this->_template_mod->get_error());
    	
    		return;
    	}
    	else
    	{
    		$this->show_message('印花删除成功!',
    					'back_list', 'index.php?app=yh_template');
    	}

    }
    
    function ajax_customs_info(){
        $ids=$_GET['ids'];
        $did = intval($_GET['did']);
        $cData=$this->_customs_mod->findAll("cst_id IN ({$ids})");
        
        $order = array();
        
        foreach($cData as $key => $val){
        	$cData[$key]['order'] = $order[$key];
        }
        $this->assign('goods_list',$cData);
        $this->display(YH."yh_template.item.html");
    }
    
}

?>
    
