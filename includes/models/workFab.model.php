<?php
class WorkFabModel extends BaseModel
{
	var $table  = 'work_fabric';
    var $prikey = 'id';
    var $alias  = 'fab';
    var $_name  = 'fab';
    var $_prefix = "diy_";
    var $_relation = array(
        'belongs_to_fab' => array(
            'model'             => 'fabs',
            'type'              => HAS_ONE,
            'foreign_key'       => 'ID',
            "refer_key"         => "item_id"
        ),
    );
}
