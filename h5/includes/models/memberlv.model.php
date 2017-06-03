<?php

/* 会员 member */
class MemberLvModel extends BaseModel
{
    var $table  = 'member_lv';
    var $prikey = 'member_lv_id';
    var $_name  = 'member_lv';
    var $_type = array('member', 'joining', 'supplier', 'service');
    var $_typename = array('member'=>'消费者','supplier'=>'创业者','service'=>'量体派工');



/**
 * 获取各类型会员的初始等级
 * @param array 	$params		lv_type：'member', 'joining', 'supplier', 'service' | 默认等级
 * @return array 	等级信息
 */
    /*会员默认等级*///     $member_lv_mod =& m('memberlv');    /* 类型 lv_type：'member'=>'消费者','supplier'=>'创业者','service'=>'量体派工'| member默认等级 */    /* 返回array ： $m_lv['member_lv_id'] 该类型的登记id *///     $m_lv = $member_lv_mod->get_default_level();

function get_default_level($params=array()){
	$_conditions = array();
	$params['lv_type'] = !in_array($params['lv_type'], $this->_type) ? 'member' : $params['lv_type'];
	$params['default_lv'] = 1;
	foreach ($params as $_field => $_value)
	{
		$_conditions[] = "{$_field}='{$_value}'";
	}
	$_conditions = implode(' and ', $_conditions);
// 	echo $_conditions;
	$rows = $this->find(array(
			'conditions' => $_conditions,
	));
	return $rows ? current($rows) : array();

}

/*等级折扣 如果8.00，表示80%，可有两位小数点*/// $member_lv_mod =& m('memberlv');// $member_lv_id = 1;// $disCount = $member_lv_mod->get_leve_dis_count($member_lv_id);/* return sting | 8.00 */
function get_leve_dis_count($member_lv_id){
	$rows = $this->find(array(			'conditions' => 'member_lv_id='.$member_lv_id,
			'fields' => 'dis_count',	));
	return isset($rows[$member_lv_id]['dis_count']) ? $rows[$member_lv_id]['dis_count'] : 0;
}

/**
 * 自动更新等级级别
 * 根据类型所需经验值 自动更新等级
 * 
 * @param int 	$uid
 * @param sting $type
 * @param int 	$experience		预留字段
 */
function auto_level($uid,$type,$experience=1){
	
	if (!$uid || !$type || !$experience) return ;
	if (!in_array($type, $this->_type)) return ;
// 	echo $type;
	switch ($type)
	{
		case 'member':
			$user_mod =& m('member');
			$info = $user_mod->get_info($uid);
			break;
		case 'joining'://加盟商3
			$serve_mod =& m('serve');
			$info = $serve_mod->get('userid='.$uid.' and serve_type=3');
			
			break;
// 		case 'supplier'://供应商
// 			$store_mod =& m('store');
// 			$info = $store_mod->get_info($uid);
// 			break;
		case 'service'://服务点2
			$serve_mod =& m('serve');
			$info = $serve_mod->get('userid='.$uid.' and serve_type=2');
				break;
		default:
			$info = array();
			break;
	}
	if (!$info) return ;
	
	//经验值
	$score = intval($info['experience']);
	
	//所有等级
	$sdf_lv = $this->findAll(array("conditions" => "lv_type='{$type}'"));
	
	//计算下一等级
	$member_lv_id = 0;
	if ($sdf_lv){
		foreach($sdf_lv as $sdf){
			if($score>=$sdf['experience']) {
				$member_lv_id = intval($sdf['member_lv_id']);
			}
		}
	}
	
	if ('member' == $type){
		//更新级别
		if ($member_lv_id > $info['member_lv_id']){
			$user_mod->edit('user_id='.$uid,array('member_lv_id'=>$member_lv_id));
			//log ?
		}
	}
	
		
}

}
?>