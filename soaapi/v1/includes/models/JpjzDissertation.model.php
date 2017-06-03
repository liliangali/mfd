<?php
/**
 * 广告数据模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: JpjzDissertation.model.php 4382 2015-06-01 06:21:22Z lil $
 * @copyright Copyright 2014 mfd.com
 * @package ad.model.php
 */
class JpjzDissertationModel extends BaseModel
{
    var $table  = 'jpjz_dissertation';
    var $prikey = 'id';
    var $_name  = 'JpjzDissertation';

    /* 表单自动验证 */
    var $_autov = array(
        
    );
    
    
    var $_relation = array(
    
        // 一个用户有多个订单
        'has_order' => array(
            'model' => 'order',
            'type' => HAS_MANY,
            'foreign_key' => 'user_id',
            'dependent' => true
        ),
        
        
    );
   
    
    
    
    
    
}

?>