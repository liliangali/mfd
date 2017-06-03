<?php
/* 会员 member */
class GoodscustompartModel extends BaseModel
{
    var $table  = 'goods_custom_part';
    var $prikey = 'id';
    var $_name  = 'goods_custom_part';

    
	function unique($part_name,$fitting_id) {
    	$conditions = "part_name = '$part_name'". " AND part_id != ".$part_id." AND cate_id=$cate_id";
    	return (count($this->find($conditions)) == 0);
    }
}