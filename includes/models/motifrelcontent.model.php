<?php
/**
*栏目关联内容模型类
*@version 1.0
*@author cyrus <2621270755@qq.com>
*@date 2016年5月16日
*@return 
*/
class MotifRelContentModel extends BaseModel
{
    var $table      =   'motif_rel_content';
    var $prikey     =   'id';
    var $_name ='motif_rel_content';
    var $alias='mrc';
    var $_relation = array (
    		//一张图片对应一个栏目
    		'has_motif' => array (
    				'model' => 'motif',
    				'type' => HAS_ONE,
    				'foreign_key' => 'id',
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