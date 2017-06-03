<?php
class CustomattributeApp extends BackendApp
{
    var $_cusattribute_mod;
    var $_custype_mod;
    function __construct()
    {
        parent::BackendApp();
        $this->_custype_mod =&m("customtype");
        $this->_cusattribute_mod =& m('customattribute');
    }

    function index()
    {
        $type_id = intval($_GET['type_id']);
        $list = $this->_cusattribute_mod->find(array(
            "conditions" => "attr.type_id = '{$type_id}'",
            'join'       => "belongs_to_type",
            'fields'     => "attr.*,t.type_name"
        ));
    	$this->assign("type_id", $type_id);
        $this->assign("list", $list);
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'style'  => 'res:style/jqtreetable.css',
    			'script' => 'jquery.plugins/jquery.validate.js'
    	));
    	$this->display('customattribute.index.html');
    }
    
    function add()
    {
    	if (!IS_POST)
    	{
    	    $type_id = intval($_GET['type_id']);
    	    $typelist = $this->_custype_mod->find();
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$types = array();
    		foreach($typelist as $key => $val){
    		    $types[$val["type_id"]] = $val["type_name"];
    		}
    		$data = array(
    		    "type_id" => $type_id,
    		);
    		$this->assign("types", $types);
    		$this->assign("data", $data);
    		$this->display('customattribute.form.html');
    	}
    	else 
    	{
    		$data = array(
    		    'type_id'     => intval($_POST['type_id']),
    		    'attr_name'   => trim($_POST['attr_name']),
    		    'attr_values' => trim($_POST['attr_values']),
    		    'sort_order'  => intval($_POST['sort_order']),
    		    'alias'       => trim($_POST['alias']),
    		);

    		if(!$this->_cusattribute_mod->add($data))
    		{
    			$this->show_message($this->_cusattribute_mod->_errors);
    		}
    		
    		$this->show_message('添加成功',
    				'返回属性列表',    'index.php?app=customattribute&type_id='.$data["type_id"],
    				'continue_add', 'index.php?app=customattribute&amp;act=add&type_id='.$data["type_id"]
    		);
    		
    	}
    }
    
    /**
     * 修改类型
     */
    function edit()
    {
    	$attr_id = intval($_REQUEST['attr_id']);
    	if (!IS_POST)
    	{
    		if (!$attr_id)
    		{
    			$this->show_warning("你要修改的属性不存在");
    			return;
    		}
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$attr_info = $this->_cusattribute_mod->get_info($attr_id);
    		
    		$typelist = $this->_custype_mod->find();
    		$types = array();
    		foreach($typelist as $key => $val){
    		    $types[$val["type_id"]] = $val["type_name"];
    		}
    		$this->assign("types", $types);
    		$this->assign("data",$attr_info);
    		$this->display("customattribute.form.html");
    	}
    	else
    	{
    		$data = array(
    		    'attr_name' => trim($_POST['attr_name']),
    		    'type_id'   => intval($_POST['type_id']),
    		    'attr_values' => trim($_POST['attr_values']),
    		    'sort_order'  => intval($_POST['sort_order']),
    		    'alias'       => trim($_POST['alias']),
    		);
    		
    		if($this->_cusattribute_mod->edit($attr_id,$data) === false)
    		{
    			$this->show_message($this->_cusattribute_mod->_errors);
    		}
    
    		$this->show_message('修改成功',
    				'返回属性列表',    'index.php?app=customattribute&type_id='.$data['type_id']
    		);
    
    	}
    }
    
	/**
	 * @删除类型
	 * @return bool 
	 */
    function drop()
    {
    	$id = isset($_GET['attr_id']) ? trim($_GET['attr_id']) : '';
    	if (!$id)
    	{
    		$this->show_warning('你要删除的类型不存在');
    		return;
    	}
    	
    	$cusAttr = &m("cusAttr");
    	
    	$count = $cusAttr->_count("attr_id='{$id}'");
    	if($count){
    	    $this->show_warning('该属性已关联样衣，无法删除');
    	    return;
    	}

    	if (!$this->_cusattribute_mod->drop($id))
    	{
    		$this->show_warning($this->_cusattribute_mod->get_error());
    		return;
    	}
    
    	$this->show_message('删除类型成功');
    }
    
}

?>
