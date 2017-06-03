<?php

/**
 * 面料类型管理
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: fabrictype.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package fabrictype.model.php
 */
class FabricTypeModel extends BaseModel
{
    var $table  = 'fabric_type';
    var $prikey = 'type_id';
    var $_name  = 'fabrictype';

    /* 表单自动验证 */
    var $_autov = array(
        
    );

    /* 关系列表 */
    var $_relation  = array(
    		
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