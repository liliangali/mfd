<?php
/**
 *举报管理
 * @author yusw <120085474@qq.com>
 * @version $Id: complaint.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2015-1-16 alicaifeng.com
 * @package complaint.model.php
 */
class ComplaintModel extends BaseModel
{
    var $table  = 'complaint';
    var $prikey = 'id';
    var $alias  = 's';
    var $_name  = 'complaint';

    /* 关系列表 */
    var $_relation  = array(
         'belongs_to_user'  => array(
            'type'          => BELONGS_TO,
            'reverse'       => 'has_complaint',
            'model'         => 'member',
        ),
         );
}

?>