<?php
class ClientfinancedetailModel extends BaseModel
{
	var $table  = 'client_finance_detail';
	var $prikey = 'id';
	var $_name  = 'clientfinancedetail';
	var $alias ='cfd';
	var $_relation = array(
			/* 一条财务记录对应一名客户 */
			'has_client'=>array(
				'model'         => 'member',
				'type'          => HAS_ONE,
				'foreign_key'   => 'user_id',
				'refer_key'=>'user_id',
				'dependent'     => true
		   ),
	);
}