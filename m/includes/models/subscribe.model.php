<?php
class SubscribeModel extends BaseModel
{
	var $table  = 'subscribe';
	var $prikey = 'idsubscribe';
	var $_name  = 'subscribe';
	
		var $_relation = array(
		
        'has_member' => array(
            'model'         => 'member',
            'type'          => HAS_ONE,
            'foreign_key'   => 'user_id',//外表
			'refer_key'		=> 'userid',//本表
        ),
        
        'has_serve' => array(
            'model'         => 'serve',
            'type'          => HAS_ONE,
            'foreign_key'   => 'idserve',//外表
			'refer_key'		=> 'idserve',//本表
        ),
        
        'has_serve_rate' => array(
            'model'         => 'serverate',
            'type'          => HAS_ONE,
            'foreign_key'   => 'idsubscribe',//外表
			'refer_key'		=> 'idsubscribe',//本表
        ),
        
        );
}