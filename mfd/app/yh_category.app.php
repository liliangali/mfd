<?php
/**
 *    印花管理控制器
 *    @author shaozizhen
 */

class Yh_categoryApp extends BackendApp
{

	var $_template_mod;
    function __construct()
    {
    	define("YH", "yh_template/");
        $this->Yh_templateApp();
    }
    function Yh_templateApp()
    {
        parent::BackendApp();

        $this->_template_mod =&m('YhCategory');
    }
	

    /**
     * 印花管理列表
     */
    function index()
    {
        $items = $this->_template_mod->find(array(
            'conditions' => '1=1',
        ));
        $this->assign('items', $items);

        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display(YH."yh_category.index.html");
    }
    
    function add()
    {

    	if (!IS_POST)
    	{
	    	$this->display(YH."yh_category.info.html");
    	}
    	else 
    	{
    		$data = array(
    				'name' => $_POST['name'],
    		
    		);
    		 
    		$id = $this->_template_mod->add($data);
    		 
    		if($id)
    		{
    			$this->show_message('印花添加成功!',
    					'back_list', 'index.php?app=yh_category');
    		}
    		else
    		{
    			$this->show_message('印花添加失败!',
    					'back_list', 'index.php?app=yh_category');
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
    		
    		$this->display(YH."yh_category.info.html");
    	}
    	else
    	{
	
    		$data = array(
    				'name' => $_POST['name'],
    		);
    		

    		$res = $this->_template_mod->edit($id, $data);
    		
    		
    		if($res)
    		{
    			$this->show_message('印花编辑成功!',
    					'back_list', 'index.php?app=yh_category');
    		}
    	}
    }

  //  function drop()
  //  {
    	
   // 	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    	
    //	if (!$this->_template_mod->drop($id))    //删除
    //	{
   // 		$this->show_warning($this->_template_mod->get_error());
    	
    //		return;
    //	}
    //	else
    //	{
    //		$this->show_message('印花删除成功!',
    //					'back_list', 'index.php?app=yh_category');
    //	}

   // }
    
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
    
