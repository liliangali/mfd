<?php
class CustomtypeApp extends BackendApp
{
    var $_custype_mod;
    function __construct()
    {
        parent::BackendApp();
        $this->_custype_mod =& m('customtype');
    }
    /**
     * 类型列表
     */
    function index()
    {
        $list = $this->_custype_mod->find();
    	
        $this->assign("list", $list);
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'style'  => 'res:style/jqtreetable.css',
    			'script' => 'jquery.plugins/jquery.validate.js'
    	));

    	$this->display('customtype.index.html');
    }
    
    /**
     * 添加类型
     */
    function add()
    {
    	if (!IS_POST)
    	{
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->display('customtype.form.html');
    	}
    	else
    	{
    		$data = array();
    		$data["type_name"] = trim($_POST['type_name']);
    		$data["sort_order"] = trim($_POST['sort_order']);
    		if(!$this->_custype_mod->add($data))
    		{
    			$this->show_message($this->_custype_mod->_errors);
    		}
    		
    		$this->show_message('添加成功',
    				'返回类型列表',    'index.php?app=customtype',
    				'continue_add', 'index.php?app=customtype&amp;act=add'
    		);
    		
    	}
    }
    
    /**
     * 修改类型
     */
    function edit()
    {
    	$type_id = intval($_REQUEST['id']);
    	if (!IS_POST)
    	{
    		if (!$type_id)
    		{
    			$this->show_warning("你要修改的类型不存在");
    		}
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$type_info = $this->_custype_mod->get_info($type_id);
    		$this->assign("info",$type_info);
    		$this->display("customtype.form.html");
    	}
    	else
    	{
    		$data = array();
    		$data["type_name"] = trim($_POST['type_name']);
    		$data["sort_order"] = trim($_POST['sort_order']);
    		if($this->_custype_mod->edit($type_id,$data) === false)
    		{
    			$this->show_message($this->_custype_mod->_errors);
    		}
    
    		$this->show_message('修改成功',
    				'返回类型列表',    'index.php?app=customtype'
    		);
    
    	}
    }
    
	/**
	 * @删除类型
	 * @return bool 
	 */
    function drop()
    {
    	$id = isset($_GET['id']) ? trim($_GET['id']) : '';
    	if (!$id)
    	{
    		$this->show_warning('你要删除的类型不存在');
    		return;
    	}
    	
    	$cusAttr = &m("cusAttr");
    	$attribute = &m("customattribute");
    
    	$attrids = $attribute->find(array(
    	   'conditions' => "type_id='{$id}'",
    	   'fields'     => "attr_id", 
    	));
    	
    	$ids = array();
    	
    	foreach($attrids as $key => $val){
    	    $ids[] = $val["attr_id"];
    	}
    	
        if($ids){
    	   $count = $cusAttr->_count("attr_id ".db_create_in($ids));
    	   if($count){
    	       $this->show_warning('该类型下属性已关联样衣，无法删除');
    	       return;
    	   }
        }
        
    	if (!$this->_custype_mod->drop($id))
    	{
    		$this->show_warning($this->_custype_mod->get_error());
    		return;
    	}
    
    	$this->show_message('删除类型成功');
    }
    
}

?>
