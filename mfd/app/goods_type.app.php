<?php
/**
 * EC51 licence
 *
 * @copyright  Copyright (c) 2007-2016 EC51.cn Inc. (http://www.ec51.cn)
 * @license  http://license.ec51.cn/ EC51 License
 */

/**---------------------------------------------------------------------
 *    产品类型控制器
* ---------------------------------------------------------------------
* @author shaozz<shaozizhen94@163.com>
* 2016-5-19
*/
class Goods_typeApp extends BackendApp
{
    var $_goodstype_mod;
    function __construct()
    {
        parent::BackendApp();
        $this->_goodstype_mod =& m('goodstype');
    }
    /**
     * 类型列表
     */
    function index()
    {
        $list = $this->_goodstype_mod->find();
    	
        $this->assign("list", $list);
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'style'  => 'res:style/jqtreetable.css',
    			'script' => 'jquery.plugins/jquery.validate.js'
    	));

    	$this->display('goodstype/goodstype.index.html');
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
    		$this->assign("speci_list",$this->specification());
    		$this->display('goodstype/goodstype.form.html');
    	}
    	else
    	{
    		$data = array();
    		$data["name"] = trim($_POST['name']);
    		$data["alias"] = trim($_POST['alias']);
    		$specId = $_POST['speci'];
            $typeId = $this->_goodstype_mod->add($data);
    		if(!$typeId)
    		{
    			$this->show_message($this->_goodstype_mod->_errors);
    		}
            if ($specId) 
            {
                $goodstypespecMdl = m("goodstypespec");
                $dataTypeSpec['type_id'] = $typeId;
                foreach ($specId as $key => $value) 
                {
                    if ($value) 
                    {
                        $dataTypeSpec['spec_id'] = $value;
                        $goodstypespecMdl->add($dataTypeSpec);
                    }
                }
            }
    		
    		$this->show_message('添加成功',
    				'返回类型列表',    'index.php?app=goods_type',
    				'continue_add', 'index.php?app=goods_type&amp;act=add'
    		);
    		
    	}
    }
    
    /**
     * 修改类型
     */
    function edit()
    {
    	$type_id = intval($_REQUEST['id']);
    	if (!$type_id)
    	{
    	    $this->show_warning("你要修改的类型不存在");
    	    return;
    	}
    	if (!IS_POST)
    	{
    	    
    	    $specificationList = $this->specification();
    	    $goodstypespecMdl = m("goodstypespec");
    	    $goodsTypeSpecList = $goodstypespecMdl->find(array(
    	        'conditions' => "type_id = $type_id",
    	        'index_key'  => "spec_id",
    	    ));
    	    foreach ((array)$specificationList as $key => $value) 
    	    {
    	        if ($goodsTypeSpecList[$key]) 
    	        {
    	            $specificationList[$key]['is_check'] = 1;
    	        }
    	    }
    	    
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$type_info = $this->_goodstype_mod->get_info($type_id);
    		$this->assign("info",$type_info);
    		$this->assign('speci_list',$specificationList);
    		$this->display("goodstype/goodstype.form.html");
    	}
    	else
    	{
    		$data = array();
    		$data["name"] = trim($_POST['name']);
    		$data["alias"] = trim($_POST['alias']);
    		if($this->_goodstype_mod->edit($type_id,$data) === false)
    		{
    			$this->show_message($this->_goodstype_mod->_errors);
    		}
    		
    		$goodstypespecMdl = m("goodstypespec");
    		$goodstypespecMdl->drop("type_id = $type_id");
    		$specId = $_POST['speci'];
    		if ($specId)
    		{
    		    $dataTypeSpec['type_id'] = $type_id;
    		    foreach ($specId as $key => $value)
    		    {
    		        if ($value)
    		        {
    		            $dataTypeSpec['spec_id'] = $value;
    		            $goodstypespecMdl->add($dataTypeSpec);
    		        }
    		    }
    		}
    		
    		
    		
    		$this->show_message('修改成功',
    				'返回类型列表',    'index.php?app=goods_type'
    		);
    
    	}
    }
    
    /**
    *规格列表
    *@author  liang.li
    */
    function specification() 
    {
        $specificationMdl = m("specification");
        $speciList = $specificationMdl->find(array(
            'conditions' => "1=1",
        ));
        return $speciList;
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
        
    	if (!$this->_goodstype_mod->drop($id))
    	{
    		$this->show_warning($this->_goodstype_mod->get_error());
    		return;
    	}
    
    	$this->show_message('删除类型成功');
    }
    
}

?>
