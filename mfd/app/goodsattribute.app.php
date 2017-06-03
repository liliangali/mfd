<?php
/**
 * EC51 licence
 *
 * @copyright  Copyright (c) 2007-2016 EC51.cn Inc. (http://www.ec51.cn)
 * @license  http://license.ec51.cn/ EC51 License
 */

/**---------------------------------------------------------------------
 *    产品类型->扩展属性控制器
* ---------------------------------------------------------------------
* @author shaozz<shaozizhen94@163.com>
* 2016-5-19
*/

class GoodsattributeApp extends BackendApp
{
    var $_goodsattribute_mod;
    var $_goodstype_mod;
    function __construct()
    {
        parent::BackendApp();
        $this->_goodstype_mod =&m("goodstype");
        $this->_goodsattribute_mod =& m('goodsattribute');
    }

    function index()
    {
    	
        $type_id = intval($_GET['type_id']);
        
        $conditions = "1=1 ";
        if ($type_id) 
        {
            $conditions = " attr.type_id = '{$type_id}' ";
        }
        $list = $this->_goodsattribute_mod->find(array(
            "conditions" => $conditions,
            'join'       => "belongs_to_type",
            'fields'     => "attr.*,t.name"
        ));
        $typelist = $this->_goodstype_mod->find();
        foreach($typelist as $key=>$value)
        {
        	$typelists[$value['type_id']]=$value['name'];
        }
        $this->assign("typelists", $typelists);
    	$this->assign("type_id", $type_id);
        $this->assign("list", $list);
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'style'  => 'res:style/jqtreetable.css',
    			'script' => 'jquery.plugins/jquery.validate.js'
    	));
    	$this->display('goodsattribute/goodsattribute.index.html');
    }
    
    function add()
    {
    	if (!IS_POST)
    	{
    	    $type_id = intval($_GET['type_id']);
    	    $typelist = $this->_goodstype_mod->find();
    	   
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		 $types = array();
    		foreach($typelist as $key => $val){
    		    $types[$val["type_id"]] = $val["name"];
    		}
    		$data = array(
    		    "type_id" => $type_id,
    		);
    		$this->assign("types", $types); 
    		$data['attr_input_type'] = 0;
    		$this->assign("attr", $data);
    		$this->display('goodsattribute/goodsattribute.form.html');
    	}
    	else 
    	{
    		$data = array(
    		    'type_id'     => intval($_POST['type_id']),
    		    'attr_name'   => trim($_POST['attr_name']),
    		    'attr_values' => trim($_POST['attr_values']),
    		    'sort_order'  => intval($_POST['sort_order']),
    		    'alias'       => trim($_POST['alias']),
    		    'attr_input_type' => $_POST['attr_input_type'],
    		);
    		if(!$this->_goodsattribute_mod->add($data))
    		{
    			$this->show_message($this->_goodsattribute_mod->_errors);
    		}
    		
    		$this->show_message('添加成功',
    				'返回属性列表',    'index.php?app=goodsattribute&type_id='.$data["type_id"],
    				'continue_add', 'index.php?app=goodsattribute&amp;act=add&type_id='.$data["type_id"]
    		);
    		
    	}
    }
    
    /**
     * 修改类型
     */
    function edit()
    {
    	$attr_id = intval($_REQUEST['attr_id']);
    	if (!$attr_id)
    	{
    	    $this->show_warning("你要修改的属性不存在");
    	    return;
    	}
    	
    	if (!IS_POST)
    	{
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$attr_info = $this->_goodsattribute_mod->get_info($attr_id);
    		
    		$typelist = $this->_goodstype_mod->find();
    		$types = array();
    		foreach($typelist as $key => $val){
    		    $types[$val["type_id"]] = $val["name"];
    		}
//   echo '<pre>';print_r($attr_info);exit; 
    		$this->assign("types", $types);
    		$this->assign("attr",$attr_info);
    		$this->display("goodsattribute/goodsattribute.form.html");
    	}
    	else
    	{
    		$data = array(
    		    'attr_name' => trim($_POST['attr_name']),
    		    'type_id'   => intval($_POST['type_id']),
    		    'attr_values' => trim($_POST['attr_values']),
    		    'sort_order'  => intval($_POST['sort_order']),
    		    'alias'       => trim($_POST['alias']),
    		    'attr_input_type' => $_POST['attr_input_type'],
    		);
    		if($this->_goodsattribute_mod->edit($attr_id,$data) === false)
    		{
    			$this->show_message($this->_goodsattribute_mod->_errors);
    		}
    
    		$this->show_message('修改成功',
    				'返回属性列表',    'index.php?app=goodsattribute&type_id='.$data['type_id']
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
    		$this->show_warning('你要删除的属性不存在');
    		return;
    	}
    	
    	if (!$this->_goodsattribute_mod->drop($id))
    	{
    		$this->show_warning($this->_goodsattribute_mod->get_error());
    		return;
    	}
    
    	$this->show_message('删除属性成功');
    }
    
}

?>
