<?php
/**
 * 广告数据模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: shufflinggroup.model.php 13272 2016-01-03 08:20:29Z yushw $
 * @copyright Copyright 2014 mfd.com
 * @package ad.model.php
 */
class ShufflinggroupModel extends BaseModel
{
    var $table  = 'shuffling_group';
    var $prikey = 'id';
    var $_name  = 'shufflinggroup';

    /* 表单自动验证 */
    var $_autov = array(
        
    );

    /* 关系列表 */
    var $_relation  = array(
    		
        // 一个店铺有多个商品
        'has_shuffling' => array(
            'model'         => 'shuffling',
            'type'          => HAS_MANY,
            'foreign_key'   => 'groups',
        ),
    );
    
   
    
    
    
    
    
}

?>