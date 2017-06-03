<?php
/**
 * 广告数据模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: shuffling.model.php 13272 2016-01-03 08:20:29Z yushw $
 * @copyright Copyright 2014 mfd.com
 * @package ad.model.php
 */
class ShufflingModel extends BaseModel
{
    var $table  = 'shuffling';
    var $prikey = 'id';
    var $_name  = 'shuffling';

    /* 表单自动验证 */
    var $_autov = array(
        
    );

    /* 关系列表 */
    var $_relation  = array(
        //属于一个分类
        'belongs_to_shufflinggroup' => array(
            'model'         => 'shufflinggroup',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'groups',
            'reverse'       => 'has_shuffling',
        ),
    );
    
}

?>