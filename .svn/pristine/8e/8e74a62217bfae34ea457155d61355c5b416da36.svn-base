<?php
class WorkImgsModel extends BaseModel
{
    var $table = 'work_imgs';
    var $prikey = 'id';
    var $_name = 'workimgs';
    /* 关系列表 */
    var $_relation  = array(
        // 一个作品图片只能属于一个作品
        'belongs_to_works' => array(
            'type' => BELONGS_TO,
            'reverse' => 'has_images',
            'model' => 'works'

        ),
    );
}