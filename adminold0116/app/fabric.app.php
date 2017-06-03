<?php
/**
 *    组件管理控制器
 *    @author liliang
 */
class FabricApp extends BackendApp
{
    var $_part_mod;
	var $_pcategory;
    function __construct()
    {
    	define("PART", "part/");
        $this->PartApp();
    }
    function PartApp()
    {
        parent::BackendApp();

        $this->_part_mod = & m('goodsfittings');
        $this->_pcategory = array(1=>'领头',2=>'上衣',3=>'裤子');
    }
	
    /**
     * 组件列表
     */
    function index() 
    {
		
    	$this->import_resource(array('script' => 'inline_edit_admin.js'));
    	$conditions = $this->_get_query_conditions(array(array(
    			'field' => 'fitting_name',
    			'equal' => 'LIKE',
    			'assoc' => 'AND',
    			'name'  => 'fitting_name',
    			'type'  => 'string',
    	),
    	
    	));
    	
		$cat_id = isset($_GET['cat_id']) ? $_GET['cat_id']:0;
		if ($cat_id)
		{
			$conditions .= " AND cat_id=$cat_id";
			$this->assign("cat_id",$cat_id);
		} 
		
    	/*更新排序*/
    	if (isset($_GET['sort']) && isset($_GET['order'])){
    		$sort  = strtolower(trim($_GET['sort']));
    		$order = strtolower(trim($_GET['order']));
    		if (!in_array($order,array('asc','desc'))){
    			$sort  = 'fitting_id';
    			$order = 'desc';
    		}
    	}else{
    		$sort  = 'fitting_id';
    		$order = 'desc';
    	}
    	
    	$page = $this->_get_page(15);
    	$part = $this->_part_mod->find(array(
    			'conditions' => "1=1 ".$conditions,
    			'limit'      => $page['limit'],
    			'order'      => "$sort $order",
    			'count'      => true,
    			));
    	if ($part)
    	{
    		foreach ($part as $k=>$v)
    		{
    			$part[$k]['cat_name'] = $this->_pcategory[$v['cat_id']];
    		}
    	}
    	$page['item_count']=$this->_part_mod->getCount();
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'script' => 'jqtreetable.js,inline_edit_admin.js,jquery.plugins/jquery.validate.js',
    			'style'  => 'res:style/jqtreetable.css'
    	));
    	$this->_format_page($page);
    	$this->assign("page_info",$page);
    	$this->assign("parts",$part);
    	$this->assign('pcategory',$this->_pcategory);	
		$this->assign("site_url",SITE_URL);
    	$this->display(PART."part.index.html");
    }
    
    /**
     * 添加组件
     */
    function add()
    {
    	if (!IS_POST)
    	{
    		//=====是否启用=====
    		$isOpen = array(1=>'启用',0=>'不启用');
    		$this->assign('is_open',$isOpen);
    		
    		//=====分类=====
    		$this->assign("pcategory",$this->_pcategory);
    		
    		//=====组件基本信息=====
    		$fitting = array('is_open'=>1,'sort_order'=>255);
    		$this->assign('fitting',$fitting);
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->display(PART."part.form.html");
    	}
    	else 
    	{
    		$data = array();
    		if (!$_POST['fitting_name'] || !$_POST['fitting_code'])
    		{
    			$this->show_warning('名称或者编号不能为空');
    			exit;
    		}
    		if (!$this->_part_mod->unique($_POST['fitting_name']))
    		{
    			$this->show_warning('name_exist');
    			exit;
    		}
    		
    		if (!$this->_part_mod->uniqueCode($_POST['fitting_code']))
    		{
    			$this->show_warning('code_exist');
    			exit;
    		}
    		$data['fitting_name'] = trim($_POST['fitting_name']);
    		$data['cat_id']       = $_POST['cat_id'];
    		$data['fitting_code'] = $_POST['fitting_code'];
    		$data['fitting_ecode'] = $_POST['fitting_ecode'];
    		$data['sort_order']   = intval($_POST['sort_order']);
    		$data['is_open']      = $_POST['is_open'];
    		$this->_part_mod->add($data);
    		 $this->show_message('add_ok',
                'back_list', 'index.php?app=part');
    	}
    }
    
    
    /**
     * 编辑组件
     */
    function edit()
    {
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	if (!IS_POST)
    	{
    		//=====是否启用=====
    		$isOpen = array(1=>'启用',0=>'不启用');
    		$this->assign('is_open',$isOpen);
    
    		//=====分类=====
    		$this->assign("pcategory",$this->_pcategory);
    
    		//=====组件基本信息=====
    		$fitting = $this->_part_mod->get($id);
    		$this->assign("fitting",$fitting);
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->display(PART."part.form.html");
    	}
    	else
    	{
    		$data = array();
    		if (!$_POST['fitting_name'] || !$_POST['fitting_code'])
    		{
    			$this->show_warning('名称或者编号不能为空');
    			exit;
    		}
    		if (!$this->_part_mod->unique($_POST['fitting_name'],$id))
    		{
    			$this->show_warning('name_exist');
    			exit;
    		}
    		$data['fitting_name'] = trim($_POST['fitting_name']);
    		$data['cat_id']       = $_POST['cat_id'];
    		$data['fitting_code'] = $_POST['fitting_code'];
    		$data['fitting_ecode'] = $_POST['fitting_ecode'];
    		$data['sort_order']   = intval($_POST['sort_order']);
    		$data['is_open']      = $_POST['is_open'];
    		$this->_part_mod->edit($id,$data);
    		$this->show_message('edit_ok',
    				'back_list', 'index.php?app=part');
    	}
    }
    
    function drop()
    {
    	$id = isset($_GET['id']) ? $_GET['id'] : 0;
    	if (!$id)
    	{
    		$this->show_warning('id_error');
    		exit;
    	}
    	$ids = explode(',', $id);
    	/* $partObject = m('goodspart');
    	$attrInfo = $partObject->get("fitting_id=$id");
    	if ($attrInfo)
    	{
    		$this->show_warning('组件已经被产品选定');
    		exit;
    	} */
    	$this->_part_mod->drop($ids);
    	$this->show_message('删除成功');
    }
    
    
	/**
     * 导出面料
     * @author liliang
     */
    function export()
    {

        $order_mod = m('part');
        $orders = $order_mod->findAll(array(
        	"conditions" => "fabric_id > 0",
        	'fields' => 'part_id,part_name,price,part_number',
        ));
        $fields_name = array('面料id','面料名称','价格','库存');
        array_unshift($orders,$fields_name);
        $this->export_to_csv($orders, 'part', 'gbk');
    }
    
}

?>
