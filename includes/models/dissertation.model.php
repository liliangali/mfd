<?php

/* 会员 member */
class DissertationModel extends BaseModel
{
    var $table  = 'dissertation';
    var $prikey = 'id';
    var $_name  = 'dissertation';

    /* 添加编辑时自动验证 */
    var $_autov = array(
    		'title' => array(
    				'required'  => true,    //必填
    				'min'       => 1,       //最短1个字符
    				'max'       => 100,     //最长100个字符
    				'filter'    => 'trim',
    		),
    		'cat' => array(
    				'required'  => true,    //必填
    		),
    		'sort_order'  => array(
    				'filter'    => 'intval',
    		),
    		'start_time'  => array(
    				'filter'    => 'intval',
    		),
    		'end_time'  => array(
    				'filter'    => 'intval',
    		),
    		'brief'  => array(
    				'max'       => 200,     //最长100个字符
    				'filter'    => 'trim',
    		),
    		'design'  => array(
    				'max'       => 200,     //最长100个字符
    				'filter'    => 'trim',
    		)
    		
    );
    
    function praiseUp($id){
    	return $this->db->query("UPDATE {$this->table} SET praise_num = praise_num+1 WHERE {$this->prikey} = {$id}");
    }
}

?>