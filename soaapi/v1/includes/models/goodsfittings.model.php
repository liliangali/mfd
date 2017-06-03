<?php

/**
 * 组件数据模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: goodsfittings.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package goodsfittings.model.php
 */
class GoodsFittingsModel extends BaseModel
{
    var $table  = 'goods_fittings';
    var $prikey = 'fitting_id';
    var $_name  = 'goodsfittings';

    /* 关系列表 */
    var $_relation  = array(
    		
    );
    
    /**
     * 判断名称是否唯一
     * @param string $fittingName
     * @param int $fittingId
     * @return boolean
     */
    function unique($fittingName, $fittingId = 0)
    {
    	$conditions = "fitting_name = '" . $fittingName . "' AND fitting_id != ".$fittingId."";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
    /**
     * 判断编号是否唯一
     * @param string $fittingCode
     * @param int $fittingId
     * @return boolean
     */
    function uniqueCode($fittingCode, $fittingId = 0)
    {
    	$conditions = "fitting_code = '" . $fittingCode . "' AND fitting_id != ".$fittingId."";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
    
}

?>