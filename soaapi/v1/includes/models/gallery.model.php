<?php

class GalleryModel extends BaseModel
{
    var $table  = 'gallery';
    var $prikey = 'id';
    var $_name  = 'gallery';
    var $_relation = array(
        'belongs_to_custom' => array(
            'model'         => 'customs',
            'type'          => BELONGS_TO,
            'dependent'     => true,
            'reverse'       => 'has_gallery',
            'foreign_key'   => 'cst_id',
            'refer_key'    => 'cst_id',
        )
    );
}

?>