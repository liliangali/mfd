<?php

/**
 * 类型表数据模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: goodstype.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package goodstype.model.php
 */
class GoodsTypeModel extends BaseModel
{
    var $table  = 'goods_type';
    var $prikey = 'type_id';
    var $_name  = 'goodstype';

    /* 表单自动验证 */
    var $_autov = array(
        
    );

    /* 关系列表 */
    var $_relation  = array(
    		
    		'belongs_to_adposition' => array(
    				'model'             => 'adposition',
    				'type'              => BELONGS_TO,
    				'foreign_key'       => 'position_id',
    				'reverse'           => 'has_ad',
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
    	$conditions = "type_name = '" . $type_name . "' AND type_id != ".$type_id."";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
   
    
    
    
    
    
}

?>