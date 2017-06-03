<?php
/**
 *    站点管理控制器
 *    @author liang.li
 */
class SiteCityApp extends BackendApp
{
    var $_city_mod;
    var $siteType;
    function __construct()
    {
    	define("CITY", "city/");
        $this->CityApp();
    }
    function CityApp()
    {
        parent::BackendApp();
		$this->siteType = array(1=>"众筹",2=>"门店");
        $this->_city_mod = & m('sitecity');
    }
	
    /**
     * 站点列表
     */
    function index() 
    {
		
    	$this->import_resource(array('script' => 'inline_edit_admin.js'));
    	$conditions = $this->_get_query_conditions(array(array(
    			'field' => 'city_name',
    			'equal' => 'LIKE',
    			'assoc' => 'AND',
    			'name'  => 'city_name',
    			'type'  => 'string',
    	),
    	));
    	
    	/*更新排序*/
    	if (isset($_GET['sort']) && isset($_GET['order']))
    	{
    		$sort  = strtolower(trim($_GET['sort']));
    		$order = strtolower(trim($_GET['order']));
    		if (!in_array($order,array('asc','desc')))
    		{
    			$sort  = 'city_id';
    			$order = 'desc';
    		}
    	}
    	else
    	{
    		$sort  = 'city_id';
    		$order = 'desc';
    	}
    	
    	$page = $this->_get_page(15);
    	$city = $this->_city_mod->find(array(
    			'conditions' => "1=1 ".$conditions,
    			'limit'      => $page['limit'],
    			'order'      => "$sort $order",
    			'count'      => true,
    	));
    	if ($city)
    	{
    		foreach ($city as $k=>$v)
    		{
    			$city[$k]['type_name'] = $this->siteType[$v['site_type']];
    		}
    	}
    	
    	
    	$page['item_count']=$this->_city_mod->getCount();
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'script' => 'jqtreetable.js,inline_edit_admin.js,jquery.plugins/jquery.validate.js',
    			'style'  => 'res:style/jqtreetable.css'
    	));
    	$this->_format_page($page);
    	$this->assign("page_info",$page);
    	$this->assign("citys",$city);
    	$this->display(CITY."city.index.html");
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
    		
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		//=====基本信息=====
    		$city = array('is_open'=>1,'sort_order'=>255,'city_name'=>'北京');
    		$this->assign('city',$city);
    		$this->assign("site_type",$this->siteType);
    		$this->display(CITY."city.form.html");
    	}
    	else 
    	{
    		$data = array();
    		if (!$_POST['city_name'])
    		{
    			$this->show_warning('名称不能为空');
    			exit;
    		}
    		
    		$data['address'] = trim(trim($_POST['address']),',');
    		$data['lng']     = $_POST['lng'];
    		$data['lat']     = $_POST['lat'];
    		$data['city_name'] = trim($_POST['city_name']);
    		$data['site_name'] = trim($_POST['site_name']);
    		$data['site_phone'] = trim($_POST['site_phone']);
    		$data['site_type'] = trim($_POST['site_type']);
    		$data['sort_order']   = intval($_POST['sort_order']);
    		$data['is_open']      = $_POST['is_open'];
    		
    		//=====获得city_code=====
    		$location = $data['lat'].",".$data['lng'];
    		$url = "http://api.map.baidu.com/geocoder?output=json&location=$location&key=UqtlIBv9TuMU6Upp4Y1YuXTd";
    		$json= file_get_contents($url);
    		$json = json_decode($json,true);
    		$data['city_code'] = $json['result']['cityCode'];
    		if (!$data['city_code'])
    		{
    			$this->show_warning('这个经纬度匹配不到对应的城市编号');
    			exit;
    		}
    		/*   暂时屏蔽
    		if (!$this->_city_mod->uniqueCode($data['city_code']))
    		{
    			$this->show_warning('一个城市只允许有一个站点');
    			exit;
    		}*/

//     	dump($data);
    		
    		$this->_city_mod->add($data);
    		$this->show_message('添加成功',
                'back_list', 'index.php?app=sitecity');
    	}
    }
    
    
    /**
     * 编辑站点
     */
    function edit()
    {
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	if (!IS_POST)
    	{
    		//=====是否启用=====
    		$isOpen = array(1=>'启用',0=>'不启用');
    		$this->assign('is_open',$isOpen);
    		//=====基本信息=====
    		$city = $this->_city_mod->get($id);
    		$this->assign("city",$city);
    		$this->assign("site_type",$this->siteType);
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->display(CITY."city.form.html");
    	}
    	else
    	{
    		$data = array();
    		if (!$_POST['city_name'])
    		{
    			$this->show_warning('名称不能为空');
    			exit;
    		}
    		/* 暂时屏蔽
    		if (!$this->_city_mod->unique($_POST['city_name'],$id))
    		{
    			$this->show_warning('城市名称已经存在');
    			exit;
    		}*/
    		
    		$data['address'] = trim(trim($_POST['address']),',');
    		$data['lng']     = $_POST['lng'];
    		$data['lat']     = $_POST['lat'];
    		$data['city_name'] = trim($_POST['city_name']);
    		$data['site_name'] = trim($_POST['site_name']);
    		$data['site_phone'] = trim($_POST['site_phone']);
    		$data['site_type'] = trim($_POST['site_type']);
    		$data['sort_order']   = intval($_POST['sort_order']);
    		$data['is_open']      = $_POST['is_open'];
    		
    		//=====获得city_code=====
    		$location = $data['lat'].",".$data['lng'];
    		$url = "http://api.map.baidu.com/geocoder?output=json&location=$location&key=UqtlIBv9TuMU6Upp4Y1YuXTd";
    		$json= file_get_contents($url);
    		$json = json_decode($json,true);
    		$data['city_code'] = $json['result']['cityCode'];
    		if (!$data['city_code'])
    		{
    			$this->show_warning('这个经纬度匹配不到对应的城市编号');
    			exit;
    		}
    		/*暂时屏蔽
    		if (!$this->_city_mod->uniqueCode($data['city_code'],$id))
    		{
    			$this->show_warning('一个城市只允许有一个站点');
    			exit;
    		}*/
    		
    		$this->_city_mod->edit($id,$data);
    		$this->show_message('编辑成功',
    				'back_list', 'index.php?app=sitecity');
    	}
    }
    
    function drop()
    {
    	$id = isset($_GET['id']) ? $_GET['id'] : 0;
    	if (!$id)
    	{
    		$this->show_warning('id错误');
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
    	$this->_city_mod->drop($ids);
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
