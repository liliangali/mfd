<?php

/**
 * 大礼包卡类型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: spreecardtype.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package fabrictype.model.php
 */
class SpreeCardTypeModel extends BaseModel
{
    var $table  = 'spree_card_type';
    var $prikey = 'id';
    var $_name  = 'spreecardtype';

    /* 表单自动验证 */
    var $_autov = array(
        
    );

    /* 关系列表 */
    var $_relation  = array(
    		// 一个店铺有多个商品
    		'has_spree' => array(
    				'model'         => 'spreecontent',
    				'type'          => HAS_MANY,
    				'foreign_key'   => 'card_type_id',
    				'dependent' => true
    		),
    );
    
    /**
     * 判断名称是否唯一
     * @param string $addName
     * @param int $addId
     * @return boolean
     */
    function unique($type_name, $type_id = 0)
    {
    	$conditions = "type_name = '" . $type_name . "' AND id != ".$type_id."";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
   
    
    
    
    
    
}

?>