<?php

/* 订单量体数据 */
class MemberfigureModel extends BaseModel
{
    var $table  = 'member_figure';
    var $prikey = 'figure_sn';
    var $_name  = 'member_figure';
    
    
    
	    var $_relation  = array(
	    		
	    		//=====一个量体数据 属于一个会员=====
	    		'belongs_to_user'  => array(
	    				'type'          => BELONGS_TO,
	    				'reverse'       => 'has_member_figure',
	    				'model'         => 'member',
	    		),
	    );
}

?>