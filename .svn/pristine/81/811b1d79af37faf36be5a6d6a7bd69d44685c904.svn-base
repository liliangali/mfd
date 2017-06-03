<?php

/* 文章 process */
class ProcessModel extends BaseModel
{
    var $table  = 'process';
    var $prikey = 'process_id';
    var $_name  = 'process';

    /* 添加编辑时自动验证 */
    var $_autov = array(
        'title' => array(
            'required'  => true,    //必填
            'min'       => 1,       //最短1个字符
            'max'       => 100,     //最长100个字符
            'filter'    => 'trim',
        ),
        'sort_order'  => array(
            'filter'    => 'intval',
        ),
        'cate_id'  => array(
            'min'       => 1,
            'required'  => true,    //必填
        ),
        'link'  => array(
            'filter'    => 'trim',
            'max'       => 255,     //最长100个字符
        ),
    );

    var $_relation = array(
        // 一篇文章只能属于一个店铺
        'belongs_to_store' => array(
            'model'             => 'store',
            'type'              => BELONGS_TO,
            'foreign_key'       => 'store_id',
            'reverse'           => 'has_process',
        ),
        // 一篇文章只能属于一个文章分类
        'belongs_to_pcategory' => array(
            'model'             => 'pcategory',
            'type'              => BELONGS_TO,
            'foreign_key'       => 'cate_id',
            'reverse'           => 'has_process',
        ),
         //一个文章对应多个上传文件
        'has_uploadedfile' => array(
            'model'             => 'uploadedfile',
            'type'              => HAS_MANY,
            'foreign_key' => 'item_id',
            'ext_limit' => array('belong' => BELONG_process),
            'dependent' => true
        ),
    );
    
    function get_process_info($id,$is_show = 1){
 		if (!$id) return array();
 		
 		
 		$rows = $this->find(array(
 				'conditions' => "process_id = '" . $id . "' AND if_show = $is_show",
 				'fields' => '*',
 		));
 		return $rows ? current($rows) : array();
 		
    }

}

?>