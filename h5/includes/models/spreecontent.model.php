<?php

/**
 * 大礼包卡模型
 * @author liang.li
 *
 */
class SpreeContentModel extends BaseModel
{
    var $table  = 'spree_content';
    var $prikey = 'id';
    var $_name  = 'spreecontent';
    var $temp; // 临时变量
    var $_relation = array(
    		// 一个商品只能属于一个店铺
    		'belongs_to_spree_type' => array(
    				'model'         => 'spreecardtype',
    				'type'          => BELONGS_TO,
    				'foreign_key'   => 'card_type_id',
    				'reverse'       => 'has_spree',
    		),
    );

    var $_autov = array(
    );
    

  	
    /**
     * 判断卡号是否唯一
     * @param string $addName
     * @param int $addId
     * @return boolean
     */
    function unique($card_number, $card_id = 0)
    {
    	$conditions = "card_number = '" . $card_number . "' AND card_id != ".$card_id."";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
   
}


?>