<?php
class ServedetailModel extends BaseModel
{
	var $table  = 'serve_detail';
	var $prikey = 'id';
	var $_name  = 'serve_detail';
	var $_relation = array(
	'has_serve' => array(
            'model'         => 'serve',
            'type'          => HAS_ONE,
            'foreign_key'   => 'idserve',
			'refer_key'		=> 'idserve'
        ),
        
        
        );
}