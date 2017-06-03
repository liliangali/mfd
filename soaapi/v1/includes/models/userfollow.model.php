<?php

/* 好友关系 user_follow */
class UserfollowModel extends BaseModel
{
    var $table  = 'user_follow';
    var $prikey = 'id';
    var $_name  = 'user_follow';
//     var $_autov = array(
//         'uid' =>  array(
//             'required'  => true,
//             'filter'    => 'intval',
//         ),
//         'follow_uid' =>  array(
//             'required'  => true,
//             'filter'    => 'intval',
//         )
//     );
	function get($params){
		
		$_conditions = array();
		foreach ($params as $_field => $_value)
		{
			$_conditions[] = "{$_field}='{$_value}'";
		}
		$_conditions = implode(' and ', $_conditions);
		
		
		$rows = $this->find(array(
				'conditions' => $_conditions,
		));
		return $rows ? current($rows) : array();
		
	}
}
