<?php

/**
 * 属性规格模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: attribute.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package attribute.model.php
 */
/**
 * 属性规格模型
 * @see ProductsModel
 * @version 1.0.0 (2014-11-18)
 * @author liang.li <1184820705@qq.com>
 * @package attribute.model.php
 */
class AttributeModel extends BaseModel
{
    var $table  = 'attribute';
    var $prikey = 'attr_id';
    var $alias  = 'attr';
    var $_name  = 'attribute';
    
    var $_relation = array(
    
    		// 
    		'has_goodsattr' => array(
    				'model'         => 'goodsattr',
    				'type'          => HAS_MANY,
    				'foreign_key'   => 'attr_id',
    				'dependent'     => true
    		),
    );
    
	/**
     * 判断名称是否唯一
     * @param string $attrName
     * @param int $type_id 类型id
     * @param int $attr_id 属性id
     * @return boolean
     */
    function unique($attr_name, $type_id,$attr_id=0)
    {
    	$conditions = "attr_name = '" . $attr_name . "' AND type_id = ".$type_id." AND attr_id != $attr_id";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
}

?>