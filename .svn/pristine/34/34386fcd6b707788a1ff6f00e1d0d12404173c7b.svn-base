<?php

/**
 * 广告位置数据模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: adposition.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package adposition.model.php
 */
/**
 * 广告位置数据模型
 * @see ProductsModel
 * @version 1.0.0 (2014-11-18)
 * @author liang.li <1184820705@qq.com>
 * @package adposition.model.php
 */
class AdPositionModel extends BaseModel
{
    var $table  = 'ad_position';
    var $prikey = 'position_id';
    var $_name  = 'adposition';

    /* 表单自动验证 */
    var $_autov = array(
        
    );

    /* 关系列表 */
    var $_relation  = array(
    		
    		'has_ad' => array(
    				'model'        => 'ad',
    				'type'         => HAS_MANY,
    				'foreign_key'  => 'position_id',
    				'dependent' => true
    		),
    );
    
    
    /**
     * 判断名称是否唯一
     * @param string $position_name
     * @param int $position_id
     * @return boolean
     */
    function unique($positionName, $positionId = 0)
    {
    	$conditions = "position_name = '" . $positionName . "' AND position_id != ".$positionId."";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
    
    
    
    
}

?>