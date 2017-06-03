<?php

/**
 * 类型管理
 * @author liang.li
 * @date   14-11-19
 */
class GoodsTypeApp extends BackendApp
{
	var $_type_mod;
    function __construct()
    {
        $this->TypeApp();
    }

    function TypeApp()
    {
        parent::__construct();
        $this->_type_mod =& m('goodstype');
        define("TYPE", "type/");
    }

    function index()
    {
		$typeList = $this->_type_mod->find(array(
		'conditions' => "",
		));
		$this->assign("type_list",$typeList);
		$this->import_resource(array('script' => 'inline_edit_admin.js'));
        $this->display(TYPE.'type.index.html');
    }
    
    function view()
    {
    	$id = intval($_GET['id']);
    	if($id <=0){
    		show_message("非法操作!");
    		return;
    	}
    
		$apply = $this->_apply_mod->find(array(
            'join' => 'belongs_to_user',
            'fields' => 'this.*,member.user_name',
            'conditions' => "id='{$id}'",
        ));
		
		
		
		$row =  $apply ? current($apply) : array();
		 
		$this->assign('apply', $row);
	
    	$this->import_resource(array(
    			'script' => 'jqtreetable.js,inline_edit_admin.js',
    			'style'  => 'res:style/jqtreetable.css'
    	));
    
    	$this->display('apply.view.html');
    }
    
    
    /**
     * 添加类型
     */
    function add()
    {
    	if (!IS_POST)
    	{
    		$this->display(TYPE."type.form.html");
    	}
    	else
    	{
    		$typeName = trim($_POST['type_name']);
    		if (!$typeName)
    		{
    			$this->show_warning('类型名称不能为空');
    			exit;
    		}
    		if (!$this->_type_mod->unique($typeName))
    		{
    			$this->show_warning('类型名称已经存在');
    			exit;
    		}
    		
    		$data = array();
    		$data['type_name'] = $typeName;
    		$this->_type_mod->add($data);
    		  $this->show_message('编辑成功',
                'back_list', 'index.php?app=goodstype');
    	}
    	
    }
    
    /**
     * 编辑类型
     */
    function edit()
    {
    	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
    	if (!IS_POST)
    	{
    		$this->assign('type',$this->_type_mod->get($id));
    		$this->display(TYPE."type.form.html");
    	}
    	else
    	{
    		$typeName = trim($_POST['type_name']);
    		if (!$typeName)
    		{
    			$this->show_warning('类型名称不能为空');
    			exit;
    		}
    		if (!$this->_type_mod->unique($typeName))
    		{
    			$this->show_warning('类型名称已经存在');
    			exit;
    		}
    
    		$data = array();
    		$data['type_name'] = $typeName;
    		$this->_type_mod->edit($id,$data);
    		$this->show_message('编辑成功',
    				'back_list', 'index.php?app=goodstype');
    	}
    	 
    }
    
    /**
     * 删除类型
     */
    function drop()
    {
    	$id = isset($_GET['id']) ? $_GET['id'] : 0;
    	if (!$id) 
    	{
    		$this->show_warning('id错误');
    		exit;
    	}
    	
    	$attrObject = m('attribute');
    	$attrInfo = $attrObject->get("type_id=$id");
    	if ($attrInfo) 
    	{
    		$this->show_warning('该类型存在属性 不允许删除');
    		exit;
    	}
    	$this->_type_mod->drop($id);
    	$this->show_message('删除成功');
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
  
    
}

?>
