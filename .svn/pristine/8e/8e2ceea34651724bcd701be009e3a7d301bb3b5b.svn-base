<?php

/**
 * 用户收货地址
 */

if (!defined('IN_ECM'))
{
    die('Hacking attempt');
}
class Address extends Object{
	
	var $member_id;
	var $_mod_address;
	var $_errors;
	function __construct($member_id){
		$this->member_id = $member_id;
		$this->_mod_address = &m("address");
	}
	
	/**
	 * 地址列表
	 * @author yhao.bai
	 * @return arr
	 */
	function _list(){
		if(!$this->member_id) return array();
		return $this->_mod_address->find(array(
			'conditions' => "user_id = '{$this->member_id}'",
			'order'  => "addr_id DESC",
		));
	}
	
	/**
	 * 地址添加
	 * @author yhao.bai
	 * @return bool
	 */
	function add($data){
		unset($data['addr_id']);
		$data['user_id'] = $this->member_id;
		$res = $this->_mod_address->add($data);
		if($res){
			return $res;
		}else{
			/*暂行方法*/
			$this->_error("收货地址添加失败");
			return false;
		}
	}
	
	/**
	 * 地址更新
	 * @author yhao.bai
	 * @return bool
	 */
	function update($data){
		$addr_id = $data['addr_id'];
		unset($data['addr_id']);
		$this->_mod_address->edit("addr_id='{$addr_id}' AND user_id='{$this->member_id}'", $data);
	}
	
	function count(){
		
		return $this->_mod_address->_count(array(
					'conditions' => "user_id = '{$this->member_id}'",
				));
	}
	
	/**
	 * 地址删除
	 * @author yhao.bai
	 * @return bool
	 */
	function drop($id){
		if(!$this->member_id){ 
			$this->_error("非法操作！");
			return false;
		}
		
		$res = $this->_mod_address->drop("addr_id='{$id}' AND user_id = '{$this->member_id}'");
		if(!$res){
			$this->_error("非法操作！");
		}
		return $res;
	}
	
	
	/**
	 * 设置默认地址
	 * @author yhao.bai
	 * @return bool
	 */
	function defAddress($addr_id=0){
		return $this->_mod_address->get(array(
					'conditions' => "user_id ='{$this->member_id}' AND addr_id = '{$addr_id}'",
				));
		
	}

	/**
	 * 设置报错信息
	 * @author yhao.bai
	 * @return bool
	 */
	function _error($msg){
		$this->_errors = $msg;
	}
	
/*
	function _get_regions($region_id="", $has_country = true)
	{
		$regions = explode(",",$region_id);
		
		$reg1 = $has_country ? $regions[0] : 0;
		
		$reg2 = $has_country ? $regions[1] : $regions[0];
		
		$reg3 = $has_country ? $regions[2] : $regions[1];
		
		$reg4 = $has_country ? $regions[3] : $regions[2];
		
		
		$reg1_list = $this->_regions(0);
		$reg2_list     = $reg2  ? $this->_regions($reg1) : array();
		$reg3_list     = $reg3  ? $this->_regions($reg2) : array();
		$reg4_list     = $reg4  ? $this->_regions($reg3) : array();
		
		return array(
					'list' => array(
						'reg1_list' => $reg1_list,
						'reg2_list' => $reg2_list,
						'reg3_list' => $reg3_list,
						'reg4_list' => $reg4_list,
					),
					'cur' => array(
						'reg1' => $reg1,
						'reg2' => $reg2,
						'reg3' => $reg3,
						'reg4' => $reg4,
					)	
				);
	}
	
	function _regions($region_id){
		$regions = $this->_mod_region->get_list($region_id);
		if ($regions)
		{
			$tmp  = array();
			foreach ($regions as $key => $value)
			{
				$tmp[$key] = $value['region_name'];
			}
			$regions = $tmp;
		}
	
		return $regions;
	}
	*/
}
?>