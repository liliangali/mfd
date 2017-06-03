<?php
/**
 * EC51 licence
 *
 * @copyright  Copyright (c) 2007-2016 EC51.cn Inc. (http://www.ec51.cn)
 * @license  http://license.ec51.cn/ EC51 License
 */

/**---------------------------------------------------------------------
 *    产品规格->添加规格控制器
* ---------------------------------------------------------------------
* @author shaozz<shaozizhen94@163.com>
* 2016-5-20
*/

class addspecApp extends BackendApp
{
    var $_specvalues_mod;
    var $_specification;
    function __construct()
    {
        parent::BackendApp();
        $this->_specification =&m("specification");
        $this->_specvalues_mod =& m('specvalues');
    }

    function index()
    {
    	$type = $_GET['type'];
        $spec_id = intval($_GET['spec_id']);
        $list = $this->_specvalues_mod->find(array(
            "conditions" => "spec_id = '{$spec_id}'",
        ));
        $this->assign("types", $type);
    	$this->assign("spec_id", $spec_id);
        $this->assign("list", $list);
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'style'  => 'res:style/jqtreetable.css',
    			'script' => 'jquery.plugins/jquery.validate.js'
    	));
    	$this->display('addspec/addspec.index.html');
    }
    
    function add()
    {
    	$spec_id = intval($_GET['spec_id']);
    	$typeys = $_GET['type'];
    	$this->assign("typeys", $typeys);
    	if (!IS_POST)
    	{
    	    
    	    $typelist = $this->_specification->find();
    	   
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
    		$this->assign("data", $data);
    		$this->display('addspec/addspec.form.html');
    	}
    	else 
    	{
    		$data = array(
    		    'spec_value'     => trim($_POST['spec_value']),
    			'alias'       => trim($_POST['alias']),
    		    'p_order'   => trim($_POST['p_order']),
    			'spec_id'=>$spec_id,
    		   
    		);
          if($_POST['spec_image']){
	      $data['spec_image']=$_POST['spec_image'];
           }
    		if(!$this->_specvalues_mod->add($data))
    		{
    			$this->show_message($this->_specvalues_mod->_errors);
    		}
    		
    		$this->show_message('添加成功',
    				'返回规格列表',    'index.php?app=addspec&spec_id='.$spec_id.'&type='.$typeys,
    				'continue_add', 'index.php?app=addspec&amp;act=add&spec_id='.$data["spec_id"]
    		);
    		
    	}
    }
    
    /**
     * 修改类型
     */
    function edit()
    {
    	$spec_id = intval($_GET['spec_id']);
    	$spec_value_id = intval($_REQUEST['spec_value_id']);
    	$typeys = $_GET['type'];
    	$this->assign("spec_id", $spec_id);
    	$this->assign("typeys", $typeys);
    	if (!IS_POST)
    	{
    		if (!$spec_value_id)
    		{
    			$this->show_warning("你要修改的规格值不存在");
    			return;
    		}
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$spec_values = $this->_specvalues_mod->get_info($spec_value_id);
    		
    		$typelist = $this->_specification->find();
    		$types = array();
    		foreach($typelist as $key => $val){
    		    $types[$val["type_id"]] = $val["name"];
    		}
    		$this->assign("types", $types);
    		$this->assign("data",$spec_values);
    		$this->display("addspec/addspec.form.html");
    	}
    	else
    	{
    	$data = array(
    		    'spec_value'     => trim($_POST['spec_value']),
    			'alias'       => trim($_POST['alias']),
    		    'p_order'   => trim($_POST['p_order']),
    			'spec_id'=>$spec_id,
    		);
       if($_POST['spec_image']){
	      $data['spec_image']=$_POST['spec_image'];
           }
    		if($this->_specvalues_mod->edit($spec_value_id,$data) === false)
    		{
    			$this->show_message($this->_specvalues_mod->_errors);
    		}
    
    		$this->show_message('修改成功',
    				'返回规格列表',    'index.php?app=addspec&spec_id='.$spec_id.'&type='.$typeys
    		);
    
    	}
    }
    
	/**
	 * @删除类型
	 * @return bool 
	 */
    function drop()
    {
    	$id = isset($_GET['spec_value_id']) ? trim($_GET['spec_value_id']) : '';
    	if (!$id)
    	{
    		$this->show_warning('你要删除的规格值不存在');
    		return;
    	}

    	if (!$this->_specvalues_mod->drop($id))
    	{
    		$this->show_warning($this->_specvalues_mod->get_error());
    		return;
    	}
    
    	$this->show_message('删除类型成功');
    }
    
}

?>
