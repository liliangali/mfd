<?php

/* 会员 member */
class Jpjz_dissertationModel extends BaseModel
{
    var $table  = 'jpjz_dissertation';
    var $prikey = 'id';
    var $_name  = 'jpjz_dissertation';

    /* 添加编辑时自动验证 */
    var $_autov = array(
    		'title' => array(
    				'required'  => true,    //必填
    				'min'       => 1,       //最短1个字符
    				'max'       => 200,     //最长200个字符
    				'filter'    => 'trim',
    		),
    		//'cat' => array(
    				//'required'  => true,    //必填
    		//),
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
    				'max'       => 900,     //最长100个字符
    				'filter'    => 'trim',
    		),
    		'design'  => array(
    				'max'       => 900,     //最长100个字符
    				'filter'    => 'trim',
    		),
    		'is_show' => array(
    			'required'      =>true,
    		),

    );


    function praiseUp($id){
    	return $this->db->query("UPDATE {$this->table} SET praise_num = praise_num+1 WHERE {$this->prikey} = {$id}");
    }
}

?>