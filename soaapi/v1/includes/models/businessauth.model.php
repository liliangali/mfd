<?php
	/**
	 *个人认证模型
	 * @author liang.li <1184820705@qq.com>
	 * @version 1.0
	 * @copyright Copyright 2014 caifeng.com
	 * @package businessauth.app.php
	*/
class BusinessAuthModel extends BaseModel
{
	var $table  = 'business_auth';
	var $prikey = 'id';
	var $_name  = 'businessauth';
	
	var $_relation = array(
			// 一个认证属于一个会员
			'belongs_to_user' => array(
					'model'         => 'member',
					'type'          => BELONGS_TO,
					'foreign_key'   => 'user_id',
					'reverse'       => 'has_business',
			),
	);
}