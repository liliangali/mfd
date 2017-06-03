<?php
class ServedropModel extends BaseModel
{
	var $table  = 'serve_drop';
	var $prikey = 'idserve';
	var $_name  = 'serve_drop';
	
	
	var $_autov = array(
        
        'company_name'=>array(
			'filter'    => 'trim',
        ),
        'mobile'=>array(
        	'required'=>true,
        	'filter'    => 'trim',
        ),
        'email'=>array(
        	'required'=>true,
        	'filter'    => 'trim',
        ),
        'linkman'=>array(
        	'required'=>true,
        	'filter'    => 'trim',
        ),
        'rc_code'=>array(
        	'required'=>true,
        	'filter'    => 'trim',
        ),
        
        
    );

    
}