<?php

/**
 * 类型管理
 * @author liang.li
 * @date   14-11-19
 */
class AttributeApp extends BackendApp
{
	var $_attr_mod;
    function __construct()
    {
        $this->AttrApp();
    }

    function AttrApp()
    {
        parent::__construct();
        $this->_attr_mod =& m('attribute');
        define("ATTR", "attr/");
    }

    function index()
    {
    	
    	$page = $this->_get_page(10);
    	$typeId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    	if (!$typeId)
    	{
    		$this->show_warning('请先选择类型');
    		exit;
    	}
		$attrList = $this->_attr_mod->find(array(
		'conditions' => "type_id=$typeId",
		'limit'      => $page['limit'],
		'count'      => true,
		));
		//=====格式化=====
		if ($attrList)
		{
			foreach ($attrList as $k=>$v)
			{
				if ($v['attr_values'])
				{
					$valueArray = explode("\r\n", $v['attr_values']);
					$value = implode(',', $valueArray);
					$attrList[$k]['attr_values'] = $value;
				}
			}
		}
		$this->assign("attr_lists",$attrList);
		$page['item_count'] = $this->_attr_mod->getCount();
		$this->_format_page($page);
		$this->assign('page_info', $page);
		
		$typeList = $this->getType();
		$this->assign("type_list",$typeList);
		$this->assign('type_name',$typeList[$typeId]);
		$this->import_resource(array('script' => 'inline_edit_admin.js'));
        $this->display(ATTR.'attribute_list.html');
    }
    
    function getType()
    {
    	$typeObject = m('goodstype');
    	$typeList = $typeObject->find(array());
    	$type = array();
    	if ($typeList)
    	{
    		foreach ($typeList as $k=>$v)
    		{
    			$type[$k] = $v['type_name'];
    		}
    	}
    	return $type;	
    }
   
    /**
     * 添加属性
     */
    function add()
    {
    	
    	if (!IS_POST)
    	{
    		$typeId = intval($_GET['type_id']);
    		//=====属性值是否可选====
    		$attrType = array(0=>'唯一',1=>'单选',2=>'复选');
    		$this->assign('attr_type',$attrType);
    		
    		//===== 属性值录入方式====
    		$attrInputType = array(0=>'手工录入',1=>'从列表中选择',2=>'文本框');
    		$this->assign('attr_input_type',$attrInputType);
    		
    		//=====类型=====
    		$this->assign('type_list',$this->getType());
    		
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$attrRow = array('attr_input_type'=>0,'attr_type'=>0,'type_id'=>$typeId);
    		$this->assign('attr_row',$attrRow);
    		$this->display(ATTR."attribute.form.html");
    	}
    	else
    	{
    		$attrName = trim($_POST['attr_name']);
    		$typeId   = $_POST['type_id'];
    		if (!$attrName)
    		{
    			$this->show_warning('类型名称不能为空');
    			exit;
    		}
    		if (!$this->_attr_mod->unique($attrName,$typeId))
    		{
    			$this->show_warning('类型名称已经存在');
    			exit;
    		}
    		$data = array();
    		$data['attr_name'] = $attrName;
    		$data['type_id']   = $typeId;
    		$data['attr_type'] = $_POST['attr_type'];
    		$data['attr_input_type'] = $_POST['attr_input_type'];
    		$data['attr_values'] = $_POST['attr_values'];
    		$data['sort_order'] = $_POST['sort_order'];
    		$this->_attr_mod->add($data);
    		  $this->show_message('添加成功',
                'back_list', 'index.php?app=attribute&id='.$typeId);
    	}
    	
    }
    
    /**
     * 删除类型
     */
    function drop()
    {
    	$id = isset($_GET['attr_id']) ? $_GET['attr_id'] : 0;
    	if (!$id) 
    	{
    		$this->show_warning('id错误');
    		exit;
    	}
    	$ids = explode(',', $id);
    	$attrObject = m('goodsattr');
    	foreach ($ids as $k=>$v)
    	{
    		$attrInfo = $attrObject->get("attr_id=$v");
    		if ($attrInfo)
    		{
    			$this->show_warning('已经有产品使用该属性  请先删除对应产品');
    			exit;
    		}
    	}
    	
    	$this->_attr_mod->drop($ids);
    	$this->show_message('删除成功');
    }
    
    /**
     * 修改属性
     */
    function edit()
    {
    	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
    	if (!IS_POST)
    	{
    		if (!$id)
    		{
    			$this->show_warning("请选择要修改的属性");
    			exit;
    		}
    		
    		//=====属性值是否可选====
    		$attrType = array(0=>'唯一',1=>'单选',2=>'复选');
    		$this->assign('attr_type',$attrType);
    		
    		//===== 属性值录入方式====
    		$attrInputType = array(0=>'手工录入',1=>'从列表中选择',2=>'文本框');
    		$this->assign('attr_input_type',$attrInputType);
    		
    		//=====类型=====
    		$this->assign('type_list',$this->getType());
    		
    		$attrInfo = $this->_attr_mod->get($id);
    		$this->assign("attr_row",$attrInfo);
    		
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->display(ATTR."attribute.form.html");
    	}
    	else
    	{
    		$attrName = trim($_POST['attr_name']);
    		$typeId   = $_POST['type_id'];
    		if (!$attrName)
    		{
    			$this->show_warning('类型名称不能为空');
    			exit;
    		}
    		if (!$this->_attr_mod->unique($attrName,$typeId,$id))
    		{
    			$this->show_warning('类型名称已经存在');
    			exit;
    		}
    		$data = array();
    		$data['attr_name'] = $attrName;
    		$data['type_id']   = $typeId;
    		$data['attr_type'] = $_POST['attr_type'];
    		$data['attr_input_type'] = $_POST['attr_input_type'];
    		$data['attr_values'] = $_POST['attr_values'];
    		$data['sort_order'] = $_POST['sort_order'];
    		$this->_attr_mod->edit($id,$data);
    		$this->show_message('添加成功',
    				'back_list', 'index.php?app=attribute&id='.$typeId);
    	}
    }
 }
    
?>
