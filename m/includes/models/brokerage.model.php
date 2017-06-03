<?php
class BrokerageModel extends BaseModel
{
	var $table  = 'brokerage';
	var $prikey = 'id';
	var $_name  = 'brokerage';

	var $_autov = array(
        'serve'=>array(
        	'required'=>true,
			'filter'    => 'trim',
        )
    );
    
    	

}