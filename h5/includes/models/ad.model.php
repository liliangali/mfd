<?php
/**
 * 广告数据模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: ad.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package ad.model.php
 */
class AdModel extends BaseModel
{
    var $table  = 'ad';
    var $prikey = 'ad_id';
    var $_name  = 'ad';

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
    function unique($addName, $addId = 0)
    {
    	$conditions = "ad_name = '" . $addName . "' AND ad_id != ".$addId."";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
   
    
    
    
    
    
}

?>