<?php

/* 量体师模型 */
class Figure_liangtiModel extends BaseModel
{
    var $table  = 'figure_liangti';
    var $prikey = 'id';
    var $_name  = 'figure_liangti';
	var $_obj_search='liangti_id|量体师id';
    var $_relation = array(
    		// 一个量体师在一个门店下
//     		'figurel' => array(
//     				'model' => 'has_figurel', //模型的名称
//     				'type' => HAS_ONE, //关系类型
//     				'foreign_key' => 'user_id', //外键名
//     				'dependent' => true //依赖
//     				'reverse'=>
//     		),
    		// 一个量体师对一个用户
    		'has_member' => array(
    				'model'         => 'member',
    				'type'          => HAS_ONE,
    				'foreign_key'   => 'user_id',
    				'refer_key'     =>'liangti_id',
    				'dependent'     => true
    		),
        // 一个商品对应多个规格
        'has_goodsspec' => array(
            'model'         => 'goodsspec',
            'type'          => HAS_MANY,
            'foreign_key'   => 'goods_id',
            'dependent'     => true
        ),
    );
  /*
     * 判断工号是否唯一
     */
    function uniquejob($job_number, $id = 0)
    {
        $conditions = "job_number = '" . $job_number . "'";
        $id && $conditions .= " AND id <> '" . $id . "'";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
/*
 * 判断身份证是否唯一
*/
function uniqueid($ident_card, $id = 0)
{
	$conditions = "ident_card = '" . $ident_card . "'";
	$id && $conditions .= " AND id <> '" . $id . "'";
	return count($this->find(array('conditions' => $conditions))) == 0;
}


}

?>