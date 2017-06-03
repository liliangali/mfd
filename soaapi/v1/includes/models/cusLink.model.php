<?php
class CusLinkModel extends BaseModel
{
	var $table  = 'custom_link';
    var $prikey = 'id';
    var $alias  = 'link';
    var $_name  = 'link';
    var $_prefix = "diy_";
    var $_relation = array(
        'belongs_to_custom' => array(
            'model'             => 'custom',
            'type'              => HAS_ONE,
            'foreign_key'       => 'id',
            "refer_key"         => "link_id"
        ),
    );
}
