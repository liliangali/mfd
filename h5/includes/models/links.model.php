<?php

/* 主题与基本款关连表 */
class LinksModel extends BaseModel
{
    var $table  = 'links';
    var $prikey = 'id';
    var $_name  = 'links';
	
    var $_relation = array(

        'has_custom' => array(
            'model'         => 'customs',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'c_id',
            'dependent'     => true,
            'reverse'       => 'belongs_to_links',
        ),
    


        'belongs_to_user'  => array(
            'type'          => BELONGS_TO,
            'reverse'       => 'has_order',
            'model'         => 'member',
        ),

        'app_custom' => array(
            'model'         => 'suitlist',
            'type'          => HAS_ONE,
            'foreign_key'   => 'id',
            'refer_key'     => 'c_id'
        ),
    );
}

?>