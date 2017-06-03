<?php
class WorksModel extends BaseModel
{
    var $table = 'works';
    var $prikey = 'id';
    var $_name = 'works';
    var $_relation =array(
        // 一个作品拥有多个作品图片
        'has_images' => array(
            'model' => 'workimgs',
            'type' => HAS_MANY,
            'foreign_key' => 'id',
            'dependent' => true

        ),
    );
}