<?php

/**
 * 宠物管理
 */

if (!defined('IN_ECM'))
{
    die('Hacking attempt');
}
class Pet extends Object{
	
	var $member_id;
	var $_mod_pet;
	var $_errors;
	function __construct($member_id){
		$this->member_id = $member_id;
		$this->_mod_pet = &m("pet");
	}
	
	/**
	 * 宠物列表
	 * @author yhao.bai
	 * @return arr
	 */
	function _list(){
		if(!$this->member_id) return array();
		return $this->_mod_pet->find(array(
			'conditions' => "user_id = '{$this->member_id}'",
			'order'  => "pet_id DESC",
		));
	}
	
	/**
	 * 添加
	 */
	function add($data){
		unset($data['pet_id']);
		$data['user_id'] = $this->member_id;
		$res = $this->_mod_pet->add($data);
		if($res){
			return $res;
		}else{
			/*暂行方法*/
			$this->_error("添加失败");
			return false;
		}
	}
	
	/**
	 * 更新
	 */
	function update($data){
		$pet_id = $data['pet_id'];
		unset($data['pet_id']);
		$this->_mod_address->edit("pet_id='{$pet_id}' AND user_id='{$this->member_id}'", $data);
	}
	
	function count(){
		return $this->_mod_address->_count(array(
					'conditions' => "user_id = '{$this->member_id}'",
				));
	}
	
	/**
	 * 删除
	 * @author yhao.bai
	 * @return bool
	 */
	function drop($id){
		if(!$this->member_id){ 
			$this->_error("非法操作！");
			return false;
		}
		
		$res = $this->_mod_pet->drop("pet_id='{$id}' AND user_id = '{$this->member_id}'");
		if(!$res){
			$this->_error("非法操作！");
		}
		return $res;
	}
	
	
	function defPet($pet_id=0){
		return $this->_mod_pet->get(array(
					'conditions' => "user_id ='{$this->member_id}' AND pet_id = '{$pet_id}'",
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
	
}
?>