<?php
/**
*栏目模型类
*@version 1.0
*@author cyrus <2621270755@qq.com>
*@date 2016年5月16日
*@return 
*/
class MotifModel extends BaseModel
{
    var $table      =   'motif';
    var $prikey     =   'id';
    var $_name ='motif';
    var $alias='m';
    var $_relation = array (
    		//一个栏目对应多个内容(图片)
    		'has_contents' => array (
    				'model' => 'motifrelcontent',
    				'type' => HAS_MANY,
    				'foreign_key' => 'id',
    				'dependent'     => true
    		),
    		//一个栏目对应一个位置
    		'has_location'=>array(
    				'model'=>'motifgroup',
    				'type'=>HAS_ONE,
    				'foreign_key'=>'id',
    				'refer_key'=>'location_id'
    		),
    );
   /**
   *栏目置顶
   *
   *@author cyrus <2621270755@qq.com>
   *@date 2016年5月16日
   *@return 
   */
    function stick($id){
    	$info=$this->get($id);
    	$sort_order=$this->get(array(
    			'fields'=>"max(sort_order) as s"
    	));
    	$data['sort_order']=$sort_order['s']+1;
    	return $this->edit($id,$data);
    }
}

?>