<?php

/**
 * 大礼包卡模型
 * @author liang.li
 *
 */
class SpreeCardModel extends BaseModel
{
    var $table  = 'spree_card';
    var $prikey = 'card_id';
    var $_name  = 'spreecard';
    var $temp; // 临时变量
    var $_relation = array(
    		
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