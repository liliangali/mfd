<?php
class WorkAttrModel extends BaseModel
{
	var $table  = 'work_attr';
    var $prikey = 'id';
    var $alias  = 'at';
    var $_name  = 'at';
    var $_prefix = "diy_";
    var $_relation = array(
        'belongs_to_attribute' => array(
            'model'             => 'customattribute',
            'type'              => HAS_ONE,
            'foreign_key'       => 'attr_id',
            "refer_key"         => "attr_id"
        ),
    );
}
