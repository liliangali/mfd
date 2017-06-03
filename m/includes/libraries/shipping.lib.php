<?php

/**
 * RCTailor: 配送方式
 * ============================================================================
 * @author yaho.bai 2014/6/17
 */

class Shipping
{
	var $_mod_region;
	var $_mod_ship;
	var $_mod_ship_area;
	var $areas;
	
    function __construct($areas=''){
    	$this->_mod_region = &m("region");
    	$this->_mod_ship = &m("shipping");
    	$this->_mod_ship_area = &m("shippingarea");
    	
    	
    	$this->areas = !empty($areas) ? explode(",",$areas) : array();
    }
    
    /* 根据用户选择的地区，筛选出支持该地区配送的方式 */
    function shippings(){
    	
    	if(empty($this->areas)) return array();
    	
    	$regions = $this->_mod_region->get_list(0);
    	 
    	$country = current($regions);
    	 
    	$area_list = $this->_mod_ship_area->find(array(
    			'fields' => "shipping_id, areas",
    	));
    	
    	$ships = array();
    	
    	if($country['region_id']){
    		array_push($this->areas, $country['region_id']);
    	}

    	foreach($area_list as $key => $val){
    		$as = @unserialize($val['areas']);
    		$inter = @array_intersect($this->areas, $as);
    		if($inter) $ships[$val['shipping_id']] = $val['shipping_id'];
    	}
    	
    	return $this->_mod_ship->find(array(
    				'conditions' => db_create_in($ships, "shipping_id")." AND enabled =1",
    				'order'      => "sort_order DESC",
    				'fields'     => "shipping_id, shipping_name, shipping_desc"
    			));
    }
    
    /**
     * 配送信息详情
     */
    function shipInfo($id){
    	$sinfo = $this->_mod_ship->get(array(
    			'conditions' => "shipping_id='{$id}' AND enabled = 1"));
    	if(empty($sinfo)){
    		return array();
    	}
    	
    	$regions = $this->_mod_region->get_list(0);
    	
    	$country = current($regions);
    	
    	//把国家压入头部
    	if($country['region_id']){
    		array_unshift($this->areas, $country['region_id']);
    	}
    	
    	// 对 国, 省，市，区  逆向转换【区，市，省,国】
    	krsort($this->areas);

    	//获取配送区域
    	$areas = $this->_mod_ship_area->find(array(
    			 "conditions" => "shipping_id='{$id}'",
    			));
    	
    	$has_area = false;
		$ainfo = array();    	
    	//以，区，市，省，国家，的顺序 取得配送区域信息
    	try{
	    	foreach($this->areas as $key => $val){
				foreach($areas as $k => $area){
						$as = unserialize($area['areas']);
						if(in_array($val, $as)){
							 $has_area = true;
							 $ainfo = $area;
							 break;
					}
				}
	    	}
    	}catch(Exception $e){}
    	
    	if(!$has_area) return array();
    	
    	return array('sinfo' => $sinfo, 'ainfo' => $ainfo);
    }
    
}

?>