<?php
class ServeModel extends BaseModel
{
	var $table  = 'serve';
	var $prikey = 'idserve';
	var $_name  = 'serve';
	
	
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
    
	var $_relation = array(
		// 一个服务点对应一条分成等级记录
        'has_brokerage' => array(
            'model'         => 'brokerage',
            'type'          => HAS_ONE,
            'foreign_key'   => 'brokerage_level',
			'refer_key'		=>'brokerage_level'
        ),
        'has_serve_detai' => array(
            'model'         => 'servedetail',
            'type'          => HAS_ONE,
            'foreign_key'   => 'idserve',
			'refer_key'		=> 'idserve'
        ),
        'has_member' => array(
            'model'         => 'member',
            'type'          => HAS_ONE,
            'foreign_key'   => 'user_id',
			'refer_key'		=> 'userid'
        ),
        'has_member_lv' => array(
            'model'         => 'memberlv',
            'type'          => HAS_ONE,
            'foreign_key'   => 'member_lv_id',
			'refer_key'		=> 'brokerage_level'//member
        ),
	);
	
	
	/*
     * 判断名称是否唯一
     */
    function unique($company_name)
    {
        $conditions = "company_name = '" . $company_name . "'";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
	/*
     * 判断服务点代码是否唯一
     */
    function unique_rc_code($rc_code)
    {
        $conditions = "rc_code = '" . $rc_code . "'";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
	/*
     * 判断服务点名称是否唯一
     */
    function unique_serve_name($serve_name)
    {
        $conditions = "serve_name = '" . $serve_name . "'";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
}