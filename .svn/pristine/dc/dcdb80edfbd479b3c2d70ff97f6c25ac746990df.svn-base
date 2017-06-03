<?php

/**
 *    轮播图模型类
 *
 *    @author    cyrus
 *    @usage    none
 */
class ShufflingModel extends BaseModel
{
    var $table      =   'shuffling';
    var $prikey     =   'id';
   /**
   *轮播图置顶
   *
   *@author cyrus <2621270755@qq.com>
   *@date 2016年5月13日
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