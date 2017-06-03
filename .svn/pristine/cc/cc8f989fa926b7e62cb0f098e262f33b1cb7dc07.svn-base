<?php
class WorkItemModel extends BaseModel
{
	var $table  = 'work_item';
    var $prikey = 'id';
    var $alias  = 'im';
    var $_name  = 'item';
    var $_prefix = "diy_";
    
    var $_relation = array(
        'belongs_to_dict' => array(
        				'model'             => 'dict',
        				'type'              => HAS_ONE,
        				'foreign_key'       => 'id',
                        "refer_key"         => "item_id"
        ),
    );
}
