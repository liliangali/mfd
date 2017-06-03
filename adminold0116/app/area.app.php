<?php

/**
 *    配送地区管理控制器
 *
 *    @author    yhao.bai
 *    @usage    none
 */
class AreaApp extends BackendApp
{
	var $model_shipping;
	var $model_shiping_area;
	var $model_region;
	function __construct(){

		$this->model_shipping =& m('shipping');
		$this->model_shiping_area =& m('shippingarea');
		$this->model_region =& m('region');
		parent::__construct();
	}
    function index()
    {
    	
    	$shipping_id = isset($_GET['shipping_id']) ? intval($_GET["shipping_id"]) : 0;
    	
    	if(!$shipping_id){
    		$this->show_warning('Hacking Attempt');
    		return ;
    	}
    	
    	$page = $this->_get_page();
    	$areas = $this->model_shiping_area->find(array(
    			'conditions' => "shipping_id='{$shipping_id}'",
    			'order'      => 'area_id DESC',
    			'limit' => $page['limit'],
    			'count' => true,
    	));
    	
    	$page['item_count'] = $this->model_shiping_area->getCount();
    	$this->_format_page($page);
    	$this->assign('page_info', $page);
    	
    	$this->assign("areas", $areas);
    	$this->assign("shipping_id", $shipping_id);
    	$this->display('area.index.html');
    }

    /**
     *   添加配送地区
     *
     *    @author    yhao.bai
     *    @return    void
     */
    function add()
    {

    	$shipping_id = isset($_REQUEST['shipping_id']) ? intval($_REQUEST["shipping_id"]) : 0;
    	
    	if(!$shipping_id){
    		$this->show_warning('Hacking Attempt');
    		return;
    	}
    	
    	if(!IS_POST){
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js,mlselection.js',
    				'style'  => 'res:style/jqtreetable.css'
    		));
    		
    		$this->_get_regions();
    		
	    	$shipping = $this->model_shipping->get_info($shipping_id);
	    	$this->assign("shipping", $shipping);
	    	$this->display('area.form.html');
    	}
    	else
    	{
    		$data = array(
    			'area_name'   => $_POST['area_name'],
    			'first_price' => $_POST['first_price'],
    			'step_price'  => $_POST['step_price'],
    			'shipping_id' => $shipping_id,
    			'areas'       => $_POST['regions'],
    		);
    		
    		$res = $this->model_shiping_area->add($data);
    		
    		if(!$res)
    		{
    			$msg = $this->model_shiping_area->get_error();
    			$this->show_warning($msg);
    			return;
    		}
    		
    		$this->show_message("操作成功", '返回配送地区列表','index.php?app=area&shipping_id='.$shipping_id);
    	}
    }
   
    
    function edit(){
    	$area_id = isset($_REQUEST['area_id']) ? intval($_REQUEST['area_id']) : 0;
    	
    	if(!$area_id)
    	{
    		$this->show_warning('Hacking Attempt');
    		return ;
    	}
    	
    	if(!IS_POST)
    	{
    		
    		$area = $this->model_shiping_area->get_info($area_id);
    		
    		if(!$area)
    		{
    			$this->show_warning('Hacking Attempt');
    		}
    		
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js,mlselection.js',
    				'style'  => 'res:style/jqtreetable.css'
    		));
    		
    		$this->_get_regions();
    		
    		$shipping = $this->model_shipping->get_info($area["shipping_id"]);
    		
    		$areas = implode(",", unserialize($area['areas']));
    		
    		$_as = $areas ? $areas : 0;
    		$_regions = $this->model_region->find(array(
    			'conditions' => "region_id IN ($_as)",
    			'fields'     => "region_id, region_name",
    		));
    		
    		$this->assign("_regions", $_regions);
    		$this->assign("shipping", $shipping);
    		$this->assign("area", $area);
    		$this->display('area.form.html');
    	}
    	else
    	{
    		$shipping_id = isset($_POST['shipping_id']) ? intval($_POST['shipping_id']) : 0;
    		
    		if(!$shipping_id)
    		{
    			$this->show_warning('Hacking Attempt');
    			return ;
    		}
    		
    		$data = array(
    				'area_name'   => $_POST['area_name'],
    				'first_price' => $_POST['first_price'],
    				'step_price'  => $_POST['step_price'],
    				'shipping_id' => $shipping_id,
    				'areas'       => $_POST['regions'],
    		);
    		
    		$res = $this->model_shiping_area->edit($area_id,$data);
    		
    		if(!$res)
    		{
    			$msg = $this->model_shiping_area->get_error();
    			$this->show_warning($msg);
    			return;
    		}
    		
    		$this->show_message("操作成功", '返回配送地区列表','index.php?app=area&shipping_id='.$shipping_id);
    	}
    }
    
    function drop()
    {
    	$area_id = isset($_GET["area_id"]) ? intval($_GET['area_id']) : 0;
    	$shipping_id = isset($_GET["shipping_id"]) ? intval($_GET['shipping_id']) : 0;
    	if(!$area_id || !$shipping_id)
    	{
    		$this->show_warning('Hacking Attempt');
    		return ;
    	}
    	
    	$res = $this->model_shiping_area->drop($area_id);
    	
    	if(!$res)
    	{
    		$msg = $this->model_shiping_area->get_error();
    		$this->show_warning($msg);
    		return;
    	}
    	
    	$this->show_message("操作成功", '返回配送地区列表','index.php?app=area&shipping_id='.$shipping_id);
    }
    
    function _get_regions()
    {
    	$regions = $this->model_region->get_list(0);
    	if ($regions)
    	{
    		$tmp  = array();
    		foreach ($regions as $key => $value)
    		{
    			$tmp[$key] = $value['region_name'];
    		}
    		$regions = $tmp;
    	}
    	$this->assign('regions', $regions);
    }
}

?>