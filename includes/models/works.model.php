<?php
class WorksModel extends BaseModel
{
    var $table = 'works';
    var $prikey = 'id';
    var $_name = 'works';
    var $alias='w';
    var $_relation = array(
        'be_collect' => array(
            'model'         => 'member',
            'type'          => HAS_AND_BELONGS_TO_MANY,
            'middle_table'  => 'collect',
            'foreign_key'   => 'item_id',
            'ext_limit'     => array('type' => 'userwork'),
            'reverse'       => 'collect_userwork',
        ),
        'be_like' => array(
            'model'         => 'member',
            'type'          => HAS_AND_BELONGS_TO_MANY,
            'middle_table'  => 'collect',
            'foreign_key'   => 'item_id',
            'ext_limit'     => array('type' => 'userwork'),
            'reverse'       => 'work_like',
        ),
        'be_image'=>array(
            'model' => 'workimgs',
            'type' => HAS_ONE,
            'foreign_key'=>'work_id',
            'refer_key'=>'id'
        ),
        
        
        'be_comment'=>array(
            'model'=>'com_comment',
            'type'=>HAS_ONE,
            'foreign_key'=>'comment_id',
            'refer_key'=>'id'
        )
    );
}